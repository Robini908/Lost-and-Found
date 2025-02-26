<?php

namespace App\Services;

use App\Models\LostItem;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Bus;
use App\Jobs\ProcessImageFeatures;

class ItemMatchingService
{
    protected $cacheKey = 'matched_items';
    protected $cacheDuration = 1440; // 24 hours in minutes

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

    protected function calculateMatches(Collection $reportedItems, Collection $foundItems)
    {
        $matches = [];

        // Precompute text embeddings for all reported and found items
        $reportedEmbeddings = $this->precomputeTextEmbeddings($reportedItems);
        $foundEmbeddings = $this->precomputeTextEmbeddings($foundItems);

        foreach ($reportedItems as $reportedItem) {
            foreach ($foundItems as $foundItem) {
                $textSimilarity = $this->cosineSimilarity(
                    $reportedEmbeddings[$reportedItem->id],
                    $foundEmbeddings[$foundItem->id]
                );

                $imageSimilarity = $this->calculateImageSimilarity(
                    $reportedItem->images,
                    $foundItem->images
                );

                $locationSimilarity = $this->calculateLocationSimilarity(
                    $reportedItem->geolocation,
                    $foundItem->geolocation
                );

                $timeSimilarity = $this->calculateTimeSimilarity(
                    $reportedItem->date_lost,
                    $foundItem->date_found
                );

                // Normalize the weights so that they add up to 1
                $textWeight = 0.3;
                $imageWeight = 0.4;
                $locationWeight = 0.2;
                $timeWeight = 0.1;

                $similarityScore = ($textSimilarity * $textWeight) +
                    ($imageSimilarity * $imageWeight) +
                    ($locationSimilarity * $locationWeight) +
                    ($timeSimilarity * $timeWeight);

                if ($similarityScore > 0.6) {
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
     * Precompute text embeddings for a collection of items.
     *
     * @param Collection $items
     * @return array
     */
    protected function precomputeTextEmbeddings(Collection $items)
    {
        $embeddings = [];

        foreach ($items as $item) {
            $embeddings[$item->id] = $this->getTextEmbedding(
                $item->title . ' ' . $item->description
            );
        }

        return $embeddings;
    }

    /**
     * Calculate text similarity using Hugging Face API.
     *
     * @param string $text1
     * @param string $text2
     * @return float
     */
    public function calculateTextSimilarity($text1, $text2)
    {
        $embedding1 = $this->getTextEmbedding($text1);
        $embedding2 = $this->getTextEmbedding($text2);

        // Check if embeddings are valid
        if (empty($embedding1) || empty($embedding2)) {
            Log::warning("Invalid embeddings for text similarity calculation.");
            return 0; // Return 0 if embeddings are invalid
        }

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
        $cacheKey = 'text_embedding_' . md5($text); // Unique cache key for each text

        // Check if the embedding is already cached
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $apiUrl = 'https://api-inference.huggingface.co/pipeline/feature-extraction/sentence-transformers/all-MiniLM-L6-v2';
        $apiKey = env('HUGGING_FACE_API_KEY');

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $apiKey,
        ])->post($apiUrl, [
            'inputs' => $text,
        ]);

        if (!$response->successful() || !is_array($response->json())) {
            Log::error("Invalid API response: " . $response->body());
            return [];
        }

        $embedding = $response->json();

        // Cache the embedding for 24 hours
        Cache::put($cacheKey, $embedding, $this->cacheDuration);

        return $embedding;
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
        // Check if vectors are valid
        if (!is_array($vector1) || !is_array($vector2) || empty($vector1) || empty($vector2)) {
            Log::warning("Invalid vectors for cosine similarity calculation.");
            return 0; // Return 0 if vectors are invalid
        }

        // Pad the shorter vector with zeros
        $maxLength = max(count($vector1), count($vector2));
        $vector1 = array_pad($vector1, $maxLength, 0);
        $vector2 = array_pad($vector2, $maxLength, 0);

        $dotProduct = 0;
        $magnitude1 = 0;
        $magnitude2 = 0;

        for ($i = 0; $i < $maxLength; $i++) {
            $dotProduct += $vector1[$i] * $vector2[$i];
            $magnitude1 += $vector1[$i] * $vector1[$i];
            $magnitude2 += $vector2[$i] * $vector2[$i];
        }

        $magnitude1 = sqrt($magnitude1);
        $magnitude2 = sqrt($magnitude2);

        // Avoid division by zero
        if ($magnitude1 === 0 || $magnitude2 === 0) {
            Log::warning("Magnitude is zero.");
            return 0;
        }

        return $dotProduct / ($magnitude1 * $magnitude2);
    }

    /**
     * Calculate image similarity using CNN features.
     *
     * @param \Illuminate\Database\Eloquent\Collection $images1
     * @param \Illuminate\Database\Eloquent\Collection $images2
     * @return float
     */
    public function calculateImageSimilarity($images1, $images2)
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
        $apiUrl = 'https://api-inference.huggingface.co/models/google/vit-base-patch16-224';

        foreach ($images as $image) {
            try {
                $imagePath = 'lost-items/' . basename($image->image_path);
                $cacheKey = 'image_features_' . md5($imagePath); // Unique cache key for each image

                // Check if the features are already cached
                if (Cache::has($cacheKey)) {
                    $features[] = Cache::get($cacheKey);
                    continue;
                }

                if (Storage::disk('public')->exists($imagePath)) {
                    $imageData = base64_encode(Storage::disk('public')->get($imagePath));

                    $response = Http::withHeaders([
                        'Authorization' => 'Bearer ' . env('HUGGING_FACE_API_KEY'),
                    ])->timeout(60)->post($apiUrl, [
                        'inputs' => $imageData,
                    ]);

                    if ($response->successful()) {
                        $predictions = $response->json();
                        $scores = array_column($predictions, 'score');
                        $features[] = $scores;

                        // Cache the features for 24 hours
                        Cache::put($cacheKey, $scores, $this->cacheDuration);
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

        return $features;
    }

    /**
     * Calculate location similarity using the Haversine formula.
     *
     * @param array $location1
     * @param array $location2
     * @return float
     */
    public function calculateLocationSimilarity($location1, $location2)
    {
        if (!$location1 || !$location2) {
            return 0; // No location data
        }

        $earthRadius = 6371; // Earth's radius in kilometers

        $lat1 = deg2rad($location1['lat']);
        $lng1 = deg2rad($location1['lng']);
        $lat2 = deg2rad($location2['lat']);
        $lng2 = deg2rad($location2['lng']);

        $dlat = $lat2 - $lat1;
        $dlng = $lng2 - $lng1;

        $a = sin($dlat / 2) ** 2 + cos($lat1) * cos($lat2) * sin($dlng / 2) ** 2;
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        $distance = $earthRadius * $c; // Distance in kilometers

        // Normalize the distance to a similarity score
        return 1 / (1 + $distance);
    }

    /**
     * Calculate time similarity using an exponential decay function.
     *
     * @param \Carbon\Carbon $dateLost
     * @param \Carbon\Carbon $dateFound
     * @return float
     */
    public function calculateTimeSimilarity($dateLost, $dateFound)
    {
        if (!$dateLost || !$dateFound) {
            return 0; // No date data
        }

        $diff = abs($dateLost->diffInDays($dateFound));

        // Use an exponential decay function to calculate similarity
        // The similarity decreases exponentially as the difference increases
        $timeSimilarity = exp(-0.1 * $diff);

        return $timeSimilarity;
    }
}
