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
    use InteractsWithQueue, Queueable, SerializesModels, Dispatchable;

    public $imagePath; // Make this public
    public $cacheKey;  // Make this public

    /**
     * Create a new job instance.
     *
     * @param string $imagePath
     * @param string $cacheKey
     */
    public function __construct($imagePath, $cacheKey)
    {
        $this->imagePath = $imagePath;
        $this->cacheKey = $cacheKey; // Assign the cache key
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

        if (!Storage::disk('public')->exists($this->imagePath)) {
            Log::warning("Image file not found in public disk: " . $this->imagePath);
            return;
        }

        $imageData = base64_encode(Storage::disk('public')->get($this->imagePath));

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . env('HUGGING_FACE_API_KEY'),
        ])->timeout(60)->post('https://api-inference.huggingface.co/models/google/vit-base-patch16-224', [
            'inputs' => $imageData,
        ]);

        if ($response->successful()) {
            $predictions = $response->json();
            Log::info("API Response: " . json_encode($predictions));
            $scores = array_column($predictions, 'score');
            Cache::put($this->cacheKey, $scores, 1440);
        } else {
            Log::warning("No predictions found in API response for image: " . $this->imagePath);
            Log::warning("API Response: " . $response->body());
        }
    } catch (\Exception $e) {
        Log::error("Error processing image: " . $e->getMessage());
    }
}
}
