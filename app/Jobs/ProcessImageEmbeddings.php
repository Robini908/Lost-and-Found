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
use Intervention\Image\Facades\Image;
use Exception;

class ProcessImageEmbeddings implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $imageUrl;
    protected $imageId;
    protected $maxRetries;
    protected $retryDelay;
    protected $apiToken;

    public function __construct($imageUrl, $imageId)
    {
        $this->imageUrl = $imageUrl;
        $this->imageId = $imageId;
        $this->maxRetries = config('services.huggingface.max_retries', 3);
        $this->retryDelay = config('services.huggingface.retry_delay', 1000);
        $this->apiToken = config('services.huggingface.api_token');
    }

    public function handle()
    {
        $cacheKey = 'image_embedding_' . $this->imageId;

        try {
            // Download and optimize image
            $optimizedImage = $this->optimizeImage();
            if (!$optimizedImage) {
                return null;
            }

            // Get embeddings from API
            $embeddings = $this->getEmbeddings($optimizedImage);
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
            // Download image
            $imageContent = Http::get($this->imageUrl)->body();
            if (!$imageContent) {
                Log::error('Failed to download image', ['url' => $this->imageUrl]);
                return null;
            }

            // Create image instance
            $img = Image::make($imageContent);

            // Resize to 224x224 (standard size for vision models)
            $img->resize(224, 224, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });

            // Convert to JPEG format with 85% quality
            return $img->encode('jpg', 85)->encoded;
        } catch (Exception $e) {
            Log::error('Failed to optimize image', [
                'error' => $e->getMessage(),
                'image_url' => $this->imageUrl
            ]);
            return null;
        }
    }

    protected function getEmbeddings($imageData)
    {
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
