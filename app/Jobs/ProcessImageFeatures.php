<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ProcessImageFeatures implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $imagePath; // Public for serialization
    public $cacheKey;  // Public for serialization

    /**
     * Create a new job instance.
     *
     * @param string $imagePath
     * @param string $cacheKey
     */
    public function __construct($imagePath, $cacheKey)
    {
        $this->imagePath = $imagePath;
        $this->cacheKey = $cacheKey;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            Log::info("Processing image: " . $this->imagePath);

            // Check if the image exists in the public disk
            if (!Storage::disk('public')->exists($this->imagePath)) {
                Log::warning("Image file not found in public disk: " . $this->imagePath);
                return;
            }

            // Read and encode the image data
            $imageData = base64_encode(Storage::disk('public')->get($this->imagePath));

            // Make API request to Hugging Face for feature extraction
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . env('HUGGING_FACE_API_KEY'),
            ])
                ->timeout(60) // Set timeout to 60 seconds
                ->retry(3, 1000) // Retry 3 times with 1-second delay
                ->post('https://api-inference.huggingface.co/models/google/vit-base-patch16-224', [
                    'inputs' => $imageData,
                ]);

            // Handle API response
            if ($response->successful()) {
                $predictions = $response->json();

                if (is_array($predictions)) { // Fixed syntax error here
                    $scores = array_column($predictions, 'score');

                    // Cache the extracted features for 24 hours (1440 minutes)
                    Cache::put($this->cacheKey, $scores, 1440);

                    Log::info("Image features cached successfully for: " . $this->imagePath);
                } else {
                    Log::warning("Invalid predictions format for image: " . $this->imagePath);
                    Log::warning("API Response: " . json_encode($predictions));
                }
            } else {
                Log::warning("API request failed for image: " . $this->imagePath);
                Log::warning("API Response: " . $response->body());
            }
        } catch (\Exception $e) {
            Log::error("Error processing image: " . $e->getMessage());
            Log::error("Stack trace: " . $e->getTraceAsString());
        }
    }

    /**
     * Handle a job failure.
     *
     * @param \Throwable $exception
     * @return void
     */
    public function failed(\Throwable $exception)
    {
        Log::error("Job failed for image: " . $this->imagePath);
        Log::error("Error: " . $exception->getMessage());
        Log::error("Stack trace: " . $exception->getTraceAsString());
    }
}
