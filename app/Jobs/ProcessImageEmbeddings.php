<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Spatie\ImageOptimizer\OptimizerChainFactory;
use Exception;
use Illuminate\Support\Str;

class ProcessImageEmbeddings implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $imageUrl;
    protected $imageId;
    protected $maxRetries;
    protected $retryDelay;
    protected $apiToken;
    protected $imageOptimizer;

    public function __construct($imageUrl, $imageId)
    {
        $this->imageUrl = $imageUrl;
        $this->imageId = $imageId;
        $this->maxRetries = config('services.huggingface.max_retries', 3);
        $this->retryDelay = config('services.huggingface.retry_delay', 1000);
        $this->apiToken = config('services.huggingface.api_token');
        $this->imageOptimizer = OptimizerChainFactory::create();
    }

    public function handle()
    {
        $cacheKey = 'image_embedding_' . $this->imageId;

        try {
            // Download and optimize image
            $optimizedImagePath = $this->optimizeImage();
            if (!$optimizedImagePath) {
                return null;
            }

            // Get embeddings from API using the optimized image
            $embeddings = $this->getEmbeddings($optimizedImagePath);

            // Clean up the temporary file
            @unlink($optimizedImagePath);

            if ($embeddings) {
                // Cache the embeddings for future use
                Cache::put($cacheKey, $embeddings, now()->addDays(7));
                return $embeddings;
            }

            return null;
        } catch (Exception $e) {
            Log::error('Failed to process image embeddings', [
                'image_id' => $this->imageId,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    protected function optimizeImage()
    {
        try {
            // Generate a unique filename for the optimized image
            $extension = pathinfo($this->imageUrl, PATHINFO_EXTENSION) ?: 'jpg';
            $filename = Str::random(40) . '.' . $extension;
            $tempPath = storage_path('app/temp/' . $filename);
            $optimizedPath = storage_path('app/temp/optimized_' . $filename);

            // Create temp directory if it doesn't exist
            if (!file_exists(dirname($tempPath))) {
                mkdir(dirname($tempPath), 0755, true);
            }

            // Download image
            $imageContent = Http::timeout(30)->get($this->imageUrl)->body();
            if (!$imageContent) {
                Log::error('Failed to download image', ['url' => $this->imageUrl]);
                return null;
            }

            // Save the original image
            file_put_contents($tempPath, $imageContent);

            // Ensure the image is valid
            if (!getimagesize($tempPath)) {
                Log::error('Invalid image file', ['path' => $tempPath]);
                @unlink($tempPath);
                return null;
            }

            // Copy to optimization path
            copy($tempPath, $optimizedPath);
            @unlink($tempPath); // Clean up original

            // Optimize the image
            $this->imageOptimizer->optimize($optimizedPath);

            // Verify the optimized file exists and is valid
            if (!file_exists($optimizedPath) || !getimagesize($optimizedPath)) {
                Log::error('Optimization failed or produced invalid image', ['path' => $optimizedPath]);
                @unlink($optimizedPath);
                return null;
            }

            Log::info('Image optimized successfully', [
                'original_url' => $this->imageUrl,
                'optimized_size' => filesize($optimizedPath),
                'optimized_path' => $optimizedPath
            ]);

            return $optimizedPath;
        } catch (Exception $e) {
            Log::error('Failed to optimize image', [
                'error' => $e->getMessage(),
                'image_url' => $this->imageUrl
            ]);
            // Clean up any temporary files
            if (isset($tempPath) && file_exists($tempPath)) @unlink($tempPath);
            if (isset($optimizedPath) && file_exists($optimizedPath)) @unlink($optimizedPath);
            return null;
        }
    }

    protected function getEmbeddings($imagePath)
    {
        // Read the optimized image file
        $imageData = file_get_contents($imagePath);
        if (!$imageData) {
            Log::error('Failed to read optimized image', ['path' => $imagePath]);
            return null;
        }

        $base64Image = base64_encode($imageData);

        for ($attempt = 1; $attempt <= $this->maxRetries; $attempt++) {
            try {
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiToken,
                    'Content-Type' => 'application/json',
                ])->timeout(30)->post(
                    config('services.huggingface.image_model_endpoint'),
                    [
                        'inputs' => [
                            'image' => $base64Image,
                        ],
                    ]
                );

                if ($response->successful()) {
                    $result = $response->json();

                    // Handle different response formats
                    if (isset($result['image_embeds'])) {
                        return $result['image_embeds'][0];
                    } elseif (is_array($result) && isset($result[0])) {
                        return $result[0];
                    }

                    Log::warning('Unexpected response format', ['response' => $result]);
                    return null;
                }

                Log::warning("API request failed on attempt $attempt", [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);

            } catch (Exception $e) {
                Log::warning("Request failed on attempt $attempt", [
                    'error' => $e->getMessage()
                ]);
            }

            if ($attempt < $this->maxRetries) {
                $delay = $this->retryDelay * pow(2, $attempt - 1);
                sleep($delay / 1000);
            }
        }

        return null;
    }
}
