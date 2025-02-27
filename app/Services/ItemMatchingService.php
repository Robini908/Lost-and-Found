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
    protected $batchSize = 50; // Process items in batches of 50
    protected $similarityThreshold = 0.6; // Minimum similarity score to consider a match (60%)
    protected $maxImagesPerItem = 5; // Maximum number of images to process per item
    protected $weights = [
        'text' => 0.35,      // Title and description similarity
        'category' => 0.15,  // Category matching
        'image' => 0.25,     // Image similarity
        'location' => 0.15,  // Location proximity
        'time' => 0.10       // Time difference between lost and found
    ];

    /**
     * Find matches for reported/searched items against found items.
     */
    public function findMatches(Collection $reportedItems, Collection $foundItems)
    {
        // Check cache first
        $cacheKey = $this->generateCacheKey($reportedItems, $foundItems);
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $matches = collect();

        // Process each reported item
        foreach ($reportedItems as $reportedItem) {
            $itemMatches = $this->findMatchesForItem($reportedItem, $foundItems);
            $matches = $matches->concat($itemMatches);
        }

        // Sort by similarity score and filter out low-quality matches
        $matches = $matches->filter(function ($match) {
            return $match['similarity_score'] >= $this->similarityThreshold;
        })->sortByDesc('similarity_score')->values();

        // Cache the results
        Cache::put($cacheKey, $matches->all(), $this->cacheDuration);

        return $matches->all();
    }

    /**
     * Find matches for a single reported item
     */
    protected function findMatchesForItem($reportedItem, Collection $foundItems)
    {
        $matches = collect();
        $reportedItemEmbedding = $this->getTextEmbedding($reportedItem);
        $reportedItemFeatures = $this->extractImageFeatures($reportedItem->images);

        foreach ($foundItems as $foundItem) {
            // Skip if the found item is from the same user
            if ($foundItem->user_id === $reportedItem->user_id) {
                continue;
            }

            // Calculate comprehensive similarity score
            $similarityScore = $this->calculateComprehensiveSimilarity(
                $reportedItem,
                $foundItem,
                $reportedItemEmbedding,
                $reportedItemFeatures
            );

            // Only add matches that meet the minimum similarity threshold
            if ($similarityScore >= $this->similarityThreshold) {
                $matches->push([
                        'reported_item' => $reportedItem,
                        'found_item' => $foundItem,
                        'similarity_score' => $similarityScore,
                    'match_details' => $this->generateMatchDetails($reportedItem, $foundItem)
                ]);
            }
        }

        return $matches;
    }

    /**
     * Calculate comprehensive similarity between two items
     */
    protected function calculateComprehensiveSimilarity($reportedItem, $foundItem, $reportedEmbedding, $reportedFeatures)
    {
        // Get text embedding for found item
        $foundEmbedding = $this->getTextEmbedding($foundItem);

        // Get image features for found item
        $foundFeatures = $this->extractImageFeatures($foundItem->images);

        // Calculate individual similarity scores
        $similarities = [
            'text' => $this->calculateTextSimilarity($reportedEmbedding, $foundEmbedding),
            'category' => $this->calculateCategorySimilarity($reportedItem, $foundItem),
            'image' => $this->calculateBestImageSimilarity($reportedFeatures, $foundFeatures),
            'location' => $this->calculateLocationSimilarity($reportedItem->geolocation, $foundItem->geolocation),
            'time' => $this->calculateTimeSimilarity($reportedItem->date_lost, $foundItem->date_found)
        ];

        // Calculate weighted average
        return array_sum(array_map(
            fn($key) => $similarities[$key] * $this->weights[$key],
            array_keys($this->weights)
        ));
    }

    /**
     * Calculate similarity between categories
     */
    protected function calculateCategorySimilarity($item1, $item2)
    {
        if (!$item1->category_id || !$item2->category_id) {
            return 0;
        }

        return $item1->category_id === $item2->category_id ? 1 : 0;
    }

    /**
     * Calculate best image similarity across all image combinations
     */
    protected function calculateBestImageSimilarity($features1, $features2)
    {
        if (empty($features1) || empty($features2)) {
            return 0;
        }

        $maxSimilarity = 0;

        // Compare each image from item1 with each image from item2
        foreach ($features1 as $feature1) {
            foreach ($features2 as $feature2) {
                $similarity = $this->cosineSimilarity($feature1, $feature2);
                $maxSimilarity = max($maxSimilarity, $similarity);
            }
        }

        return $maxSimilarity;
    }

    /**
     * Extract features from multiple images
     */
    protected function extractImageFeatures($images)
    {
        $features = [];
        $processedCount = 0;

        foreach ($images as $image) {
            if ($processedCount >= $this->maxImagesPerItem) {
                break;
            }

            $cacheKey = 'image_features_' . md5($image->image_path);
            $cached = Cache::get($cacheKey);

            if ($cached) {
                $features[] = $cached;
            } else {
                try {
                    $imagePath = 'lost-items/' . basename($image->image_path);
                    if (Storage::disk('public')->exists($imagePath)) {
                        $imageData = base64_encode(Storage::disk('public')->get($imagePath));

                        $response = Http::withHeaders([
                            'Authorization' => 'Bearer ' . env('HUGGING_FACE_API_KEY'),
                        ])->timeout(30)->post(
                            'https://api-inference.huggingface.co/models/google/vit-base-patch16-224',
                            ['inputs' => $imageData]
                        );

                        if ($response->successful()) {
                            $prediction = $response->json();
                            $features[] = array_column($prediction, 'score');
                            Cache::put($cacheKey, end($features), $this->cacheDuration);
                        }
                    }
                } catch (\Exception $e) {
                    Log::error("Error processing image: " . $e->getMessage());
                }
            }

            $processedCount++;
        }

        return $features;
    }

    /**
     * Get text embedding for item description
     */
    protected function getTextEmbedding($item)
    {
        $text = $item->title . ' ' . $item->description;
        $cacheKey = 'text_embedding_' . md5($text);

        return Cache::remember($cacheKey, $this->cacheDuration, function () use ($text) {
            try {
        $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . env('HUGGING_FACE_API_KEY'),
                ])->timeout(30)->post(
                    'https://api-inference.huggingface.co/pipeline/feature-extraction/sentence-transformers/all-MiniLM-L6-v2',
                    ['inputs' => $text]
                );

                if ($response->successful()) {
                    return $response->json();
                }
            } catch (\Exception $e) {
                Log::error("Error getting text embedding: " . $e->getMessage());
            }

            return [];
        });
    }

    /**
     * Generate detailed match information
     */
    protected function generateMatchDetails($reportedItem, $foundItem)
    {
        return [
            'category_match' => $reportedItem->category_id === $foundItem->category_id,
            'location_distance' => $this->calculateDistance(
                $reportedItem->geolocation['lat'] ?? 0,
                $reportedItem->geolocation['lng'] ?? 0,
                $foundItem->geolocation['lat'] ?? 0,
                $foundItem->geolocation['lng'] ?? 0
            ),
            'time_difference' => $reportedItem->date_lost && $foundItem->date_found
                ? $reportedItem->date_lost->diffInDays($foundItem->date_found)
                : null,
            'image_count' => [
                'reported' => $reportedItem->images->count(),
                'found' => $foundItem->images->count()
            ]
        ];
    }

    /**
     * Calculate cosine similarity between two vectors.
     *
     * @param array $vector1
     * @param array $vector2
     * @return float
     */
    protected function cosineSimilarity($vector1, $vector2): float
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

    /**
     * Calculate text similarity between two embeddings
     */
    protected function calculateTextSimilarity($embedding1, $embedding2)
    {
        if (empty($embedding1) || empty($embedding2)) {
            return 0;
        }
        return $this->cosineSimilarity($embedding1, $embedding2);
    }

    /**
     * Calculate distance between two points
     */
    protected function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        if (!$lat1 || !$lon1 || !$lat2 || !$lon2) {
            return PHP_FLOAT_MAX;
        }

        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        return $miles * 1.609344; // Convert to kilometers
    }

    /**
     * Generate a unique cache key based on the items' timestamps
     */
    protected function generateCacheKey(Collection $reportedItems, Collection $foundItems): string
    {
        $reportedHash = md5($reportedItems->pluck('updated_at')->max() . $reportedItems->count());
        $foundHash = md5($foundItems->pluck('updated_at')->max() . $foundItems->count());
        return "matched_items_{$reportedHash}_{$foundHash}";
    }
}
