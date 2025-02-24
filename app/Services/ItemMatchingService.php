<?php

namespace App\Services;

use App\Models\LostItem;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Collection;


class ItemMatchingService
{
    protected $cacheKey = 'matched_items';
    protected $cacheDuration = 1440; // 24 hours in minutes
    protected $messages = [
        "Gathering requirements...",
        "Calculating similarity scores...",
        "Analyzing images...",
        "Matching locations...",
        "Finalizing results...",
        "Hold on a moment, this will not take long..."
    ];

    /**
     * Find matches for reported items.
     *
     * @param Collection $reportedItems
     * @param Collection $foundItems
     * @return array
     */
    public function findMatches(Collection $reportedItems, Collection $foundItems)
    {
        // Check if cached matches exist
        if (Cache::has($this->cacheKey)) {
            $cachedMatches = Cache::get($this->cacheKey);

            // Check if there are any new or updated items
            if (!$this->hasNewOrUpdatedItems($reportedItems, $foundItems, $cachedMatches)) {
                return $cachedMatches; // Return cached matches if no changes
            }
        }

        // Perform the matching logic
        $matches = $this->calculateMatches($reportedItems, $foundItems);

        // Cache the matches for 24 hours
        Cache::put($this->cacheKey, $matches, $this->cacheDuration);

        return $matches;
    }

    /**
     * Check if there are new or updated items.
     *
     * @param Collection $reportedItems
     * @param Collection $foundItems
     * @param array $cachedMatches
     * @return bool
     */
    protected function hasNewOrUpdatedItems(Collection $reportedItems, Collection $foundItems, array $cachedMatches)
    {
        // Get the latest timestamps for reported and found items
        $latestReportedTimestamp = $reportedItems->max('updated_at');
        $latestFoundTimestamp = $foundItems->max('updated_at');

        // Get the cached timestamps
        $cachedReportedTimestamp = collect($cachedMatches)->pluck('reported_item.updated_at')->max();
        $cachedFoundTimestamp = collect($cachedMatches)->pluck('found_item.updated_at')->max();

        // Check if there are new or updated items
        return $latestReportedTimestamp > $cachedReportedTimestamp || $latestFoundTimestamp > $cachedFoundTimestamp;
    }

    /**
     * Calculate matches between reported and found items.
     *
     * @param Collection $reportedItems
     * @param Collection $foundItems
     * @return array
     */
    protected function calculateMatches(Collection $reportedItems, Collection $foundItems)
    {
        $matches = [];

        foreach ($reportedItems as $reportedItem) {
            foreach ($foundItems as $foundItem) {
                $similarityScore = $this->calculateSimilarity($reportedItem, $foundItem);
                if ($similarityScore > 0.5) { // Adjust threshold as needed
                    $matches[] = [
                        'reported_item' => $reportedItem,
                        'found_item' => $foundItem,
                        'similarity_score' => $similarityScore,
                    ];
                }
            }
        }

        return $matches;
    }



    /**
     * Calculate the overall similarity score between two items.
     *
     * @param LostItem $reportedItem
     * @param LostItem $foundItem
     * @return float
     */
    public function calculateSimilarity($reportedItem, $foundItem)
    {
        // Text-based similarity
        $textSimilarity = $this->calculateTextSimilarity(
            $reportedItem->title . ' ' . $reportedItem->description,
            $foundItem->title . ' ' . $foundItem->description
        );

        // Image-based similarity
        $imageSimilarity = $this->calculateImageSimilarity(
            $reportedItem->images,
            $foundItem->images
        );

        // Location-based similarity
        $locationSimilarity = $this->calculateLocationSimilarity(
            $reportedItem->geolocation,
            $foundItem->geolocation
        );

        // Time-based similarity
        $timeSimilarity = $this->calculateTimeSimilarity(
            $reportedItem->date_lost,
            $foundItem->date_found
        );

        return ($textSimilarity * 0.5) +
            ($imageSimilarity * 0.3) +
            ($locationSimilarity * 0.1) +
            ($timeSimilarity * 0.1);
    }

    /**
     * Calculate text similarity using Hugging Face API.
     *
     * @param string $text1
     * @param string $text2
     * @return float
     */
    protected function calculateTextSimilarity($text1, $text2)
    {
        $embedding1 = $this->getTextEmbedding($text1);
        $embedding2 = $this->getTextEmbedding($text2);

        Log::info("Text 1 Embedding: " . json_encode($embedding1));
        Log::info("Text 2 Embedding: " . json_encode($embedding2));

        return $this->cosineSimilarity($embedding1, $embedding2);
    }

    /**
     * Get text embeddings from Hugging Face API.
     *
     * @param string $text
     * @return array
     */
    protected function getTextEmbedding($text)
    {
        $apiUrl = 'https://api-inference.huggingface.co/pipeline/feature-extraction/sentence-transformers/all-MiniLM-L6-v2';
        $apiKey = env('HUGGING_FACE_API_KEY');

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $apiKey,
        ])->post($apiUrl, [
            'inputs' => $text,
        ]);

        Log::info("Hugging Face API Response: " . json_encode($response->json()));

        return $response->json();
    }

    /**
     * Calculate cosine similarity between two vectors.
     *
     * @param array $vector1
     * @param array $vector2
     * @return float
     */
    protected function cosineSimilarity($vector1, $vector2)
    {
        $dotProduct = 0;
        $magnitude1 = 0;
        $magnitude2 = 0;

        for ($i = 0; $i < count($vector1); $i++) {
            $dotProduct += $vector1[$i] * $vector2[$i];
            $magnitude1 += $vector1[$i] * $vector1[$i];
            $magnitude2 += $vector2[$i] * $vector2[$i];
        }

        $magnitude1 = sqrt($magnitude1);
        $magnitude2 = sqrt($magnitude2);

        return $dotProduct / ($magnitude1 * $magnitude2);
    }

    /**
     * Calculate image similarity using CNN features.
     *
     * @param \Illuminate\Database\Eloquent\Collection $images1
     * @param \Illuminate\Database\Eloquent\Collection $images2
     * @return float
     */
    protected function calculateImageSimilarity($images1, $images2)
    {
        if ($images1->isEmpty() || $images2->isEmpty()) {
            Log::info("No images to compare.");
            return 0;
        }

        try {
            $features1 = $this->extractImageFeaturesWithCNN($images1);
            $features2 = $this->extractImageFeaturesWithCNN($images2);

            if (empty($features1) || empty($features2)) {
                Log::warning("No features extracted from images.");
                return 0;
            }

            $totalSimilarity = 0;
            $count = 0;

            foreach ($features1 as $feature1) {
                foreach ($features2 as $feature2) {
                    $similarity = $this->cosineSimilarity($feature1, $feature2);
                    if (!is_nan($similarity)) {
                        $totalSimilarity += $similarity;
                        $count++;
                    }
                }
            }

            return $count === 0 ? 0 : $totalSimilarity / $count;
        } catch (\Exception $e) {
            Log::error("Image similarity calculation failed: " . $e->getMessage());
            return 0; // Fallback to 0 if there's an error
        }
    }

    /**
     * Extract image features using TensorFlow Serving API.
     *
     * @param \Illuminate\Database\Eloquent\Collection $images
     * @return array
     */
    protected function extractImageFeaturesWithCNN($images)
    {
        $features = [];
        // Use the service name of the TensorFlow Serving container
        $apiUrl = 'http://tensorflow_serving:8501/v1/models/resnet:predict';

        foreach ($images as $image) {
            try {
                $imagePath = 'lost-items/' . basename($image->image_path);

                if (Storage::disk('public')->exists($imagePath)) {
                    $imageData = base64_encode(Storage::disk('public')->get($imagePath));

                    Log::info("Sending request to TensorFlow Serving API: " . $apiUrl);
                    Log::info("Image data length: " . strlen($imageData));

                    $response = Http::timeout(60)->post($apiUrl, [
                        'instances' => [['b64' => $imageData]],
                    ]);

                    Log::info("API Response Status: " . $response->status());
                    Log::info("API Response Body: " . $response->body());

                    if ($response->successful() && isset($response->json()['predictions'][0])) {
                        $features[] = $response->json()['predictions'][0];
                        Log::info("Features extracted for image: " . $imagePath);
                    } else {
                        Log::warning("No predictions found in API response for image: " . $imagePath);
                    }
                } else {
                    Log::warning("Image file not found in public disk: " . $imagePath);
                }
            } catch (\Exception $e) {
                Log::error("Error processing image: " . $e->getMessage());
            }
        }

        if (empty($features)) {
            Log::warning("No features extracted from images.");
        }

        return $features;
    }
    /**
     * Calculate location similarity.
     *
     * @param array $location1
     * @param array $location2
     * @return float
     */
    protected function calculateLocationSimilarity($location1, $location2)
    {
        if (!$location1 || !$location2) {
            return 0; // No location data
        }

        $distance = sqrt(
            pow($location1['lat'] - $location2['lat'], 2) +
                pow($location1['lng'] - $location2['lng'], 2)
        );

        return 1 / (1 + $distance);
    }

    /**
     * Calculate time similarity.
     *
     * @param \Carbon\Carbon $dateLost
     * @param \Carbon\Carbon $dateFound
     * @return float
     */
    protected function calculateTimeSimilarity($dateLost, $dateFound)
    {
        if (!$dateLost || !$dateFound) {
            return 0; // No date data
        }

        $diff = abs($dateLost->diffInDays($dateFound));
        return 1 / (1 + $diff);
    }
}
