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
use Carbon\Carbon;

class ItemMatchingService
{
    protected $cacheKey = 'matched_items';
    protected $cacheDuration = 1440; // 24 hours in minutes
    protected $batchSize = 50; // Process items in batches of 50
    protected $similarityThreshold = 0.4; // Reduced from 0.6 to 0.4 (40% similarity threshold)
    protected $maxImagesPerItem = 5; // Maximum number of images to process per item
    protected $weights = [
        'text' => 0.30,      // Title and description similarity
        'category' => 0.15,  // Category matching
        'image' => 0.20,     // Image similarity
        'location' => 0.20,  // Location proximity
        'attributes' => 0.10, // Brand, model, color matching
        'time' => 0.05       // Time difference between lost and found
    ];

    /**
     * Find matches for reported/searched items against found items.
     */
    public function findMatches(Collection $reportedItems, Collection $foundItems)
    {
        Log::info('Starting match finding process', [
            'reported_items_count' => $reportedItems->count(),
            'found_items_count' => $foundItems->count()
        ]);

        $cacheKey = $this->generateCacheKey($reportedItems, $foundItems);

        if (Cache::has($cacheKey)) {
            Log::info('Returning cached matches');
            return Cache::get($cacheKey);
        }

        $matches = collect();

        foreach ($reportedItems as $reportedItem) {
            Log::info('Processing reported item', [
                'item_id' => $reportedItem->id,
                'title' => $reportedItem->title,
                'type' => $reportedItem->item_type
            ]);

            // Pre-filter candidates before detailed matching
            $filteredFoundItems = $this->preFilterCandidates($reportedItem, $foundItems);

            Log::info('Filtered found items', [
                'original_count' => $foundItems->count(),
                'filtered_count' => $filteredFoundItems->count()
            ]);

            // Skip detailed matching if no candidates pass pre-filtering
            if ($filteredFoundItems->isEmpty()) {
                Log::info('No candidates passed pre-filtering for item', ['item_id' => $reportedItem->id]);
                continue;
            }

            // Extract features and embeddings once for the reported item
            $reportedEmbedding = $this->getTextEmbedding($reportedItem);
            $reportedFeatures = $this->extractImageFeatures($reportedItem->images);

            $itemMatches = $this->findMatchesForItem($reportedItem, $filteredFoundItems);

            Log::info('Found matches for item', [
                'item_id' => $reportedItem->id,
                'matches_count' => $itemMatches->count()
            ]);

            if ($itemMatches->isNotEmpty()) {
                $matches = $matches->concat($itemMatches);
            }
        }

        $matches = $matches->sortByDesc('similarity_score')->values();

        Log::info('Completed match finding process', [
            'total_matches' => $matches->count()
        ]);

        Cache::put($cacheKey, $matches, now()->addHours(24));

        return $matches;
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
            // Skip if the found item is from the same user or not marked as found
            if ($foundItem->user_id === $reportedItem->user_id || $foundItem->item_type !== LostItem::TYPE_FOUND) {
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
            if ($similarityScore !== null) {
                $matches->push($similarityScore);
            }
        }

        return $matches;
    }

    /**
     * Calculate comprehensive similarity between two items
     */
    protected function calculateComprehensiveSimilarity($reportedItem, $foundItem, $reportedEmbedding, $reportedFeatures)
    {
        $weights = [
            'text' => 0.35,
            'category' => 0.15,
            'image' => 0.20,
            'location' => 0.15,
            'time' => 0.15
        ];

        $similarities = [
            'text' => $this->calculateTextSimilarity($reportedEmbedding, $this->getTextEmbedding($foundItem)),
            'category' => $this->calculateCategorySimilarity($reportedItem, $foundItem),
            'image' => $this->calculateBestImageSimilarity($reportedFeatures, $this->extractImageFeatures($foundItem->images)),
            'location' => $this->calculateLocationSimilarity($reportedItem, $foundItem),
            'time' => $this->calculateTimeSimilarity($reportedItem->date, $foundItem->date)
        ];

        $weightedSum = 0;
        foreach ($weights as $key => $weight) {
            $weightedSum += $similarities[$key] * $weight;
        }

        // Lower the threshold to 0.4 (40%)
        if ($weightedSum < 0.4) {
            Log::info("Match score too low: {$weightedSum} for items {$reportedItem->id} and {$foundItem->id}");
            return null;
        }

        Log::info("Found potential match with score {$weightedSum} for items {$reportedItem->id} and {$foundItem->id}");

        return [
            'similarity_score' => $weightedSum,
            'reported_item' => $reportedItem,
            'found_item' => $foundItem,
            'match_details' => $this->generateMatchDetails($reportedItem, $foundItem)
        ];
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
    public function calculateBestImageSimilarity($features1, $features2)
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
    public function extractImageFeatures($images)
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
    public function getTextEmbedding($item)
    {
        // Combine all relevant text fields for better matching
        $text = implode(' ', array_filter([
            $item->title,
            $item->description,
            $item->brand,
            $item->model,
            $item->color,
            $item->condition,
            $item->additional_details ? json_encode($item->additional_details) : null
        ]));

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
     * Generate detailed match information with confidence levels
     */
    protected function generateMatchDetails($reportedItem, $foundItem)
    {
        $details = [
            'title_similarity' => $this->calculateTextSimilarityWithContext($reportedItem->title, $foundItem->title),
            'description_similarity' => $this->calculateTextSimilarityWithContext($reportedItem->description, $foundItem->description),
            'category_match' => $reportedItem->category_id === $foundItem->category_id,
            'location_similarity' => $this->calculateLocationSimilarity($reportedItem, $foundItem),
            'attribute_similarity' => $this->calculateAttributesSimilarity($reportedItem, $foundItem),
            'time_similarity' => $this->calculateTimeSimilarity(
                $reportedItem->date_lost ?? $reportedItem->created_at,
                $foundItem->date_found ?? $foundItem->created_at
            ),
        ];

        // Calculate confidence level
        $confidenceScore = 0;
        $confidenceScore += $details['title_similarity'] * 0.25;
        $confidenceScore += $details['description_similarity'] * 0.20;
        $confidenceScore += ($details['category_match'] ? 1 : 0) * 0.15;
        $confidenceScore += $details['location_similarity'] * 0.25;
        $confidenceScore += $details['attribute_similarity'] * 0.10;
        $confidenceScore += $details['time_similarity'] * 0.05;

        if ($confidenceScore >= 0.8) {
            $details['confidence'] = 'high';
        } elseif ($confidenceScore >= 0.5) {
            $details['confidence'] = 'medium';
        } else {
            $details['confidence'] = 'low';
        }

        // Add location details for better user feedback
        $details['location_details'] = [
            'reported_location' => $reportedItem->location_lost ?? $reportedItem->location_address ?? $reportedItem->area,
            'found_location' => $foundItem->location_found ?? $foundItem->location_address ?? $foundItem->area,
            'distance' => ($reportedItem->location_lat && $reportedItem->location_lng &&
                          $foundItem->location_lat && $foundItem->location_lng)
                ? round($this->calculateDistance(
                    $reportedItem->location_lat,
                    $reportedItem->location_lng,
                    $foundItem->location_lat,
                    $foundItem->location_lng
                ) / 1000, 2) . ' km'
                : null
        ];

        return $details;
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
     * Calculate location similarity considering both coordinates and text
     */
    protected function calculateLocationSimilarity($reportedItem, $foundItem)
    {
        // First, determine the location type of the reported/searched item
        $reportedLocationType = $reportedItem->location_type;

        // If the reported item uses map-based location
        if ($reportedLocationType === 'map') {
            // Ensure we have the required coordinates for the reported item
            if (!$reportedItem->location_lat || !$reportedItem->location_lng) {
                return 0.1; // Return low similarity if coordinates are missing
            }

            // If found item also uses map
            if ($foundItem->location_type === 'map' && $foundItem->location_lat && $foundItem->location_lng) {
                $distance = $this->calculateDistance(
                    $reportedItem->location_lat,
                    $reportedItem->location_lng,
                    $foundItem->location_lat,
                    $foundItem->location_lng
                );

                // Convert distance to similarity score using exponential decay
                // 5km is considered the threshold for good matches
                $coordinateSimilarity = exp(-$distance / 5000);

                // Compare addresses if available (as secondary factor)
                $addressSimilarity = 0;
                if ($reportedItem->location_address && $foundItem->location_address) {
                    $addressSimilarity = $this->calculateTextSimilarityWithContext(
                        $reportedItem->location_address,
                        $foundItem->location_address
                    );
                }

                // Weight heavily towards coordinate matching (80%) with address as supporting factor (20%)
                return ($coordinateSimilarity * 0.8) + ($addressSimilarity * 0.2);
            }

            // If found item uses area, try to match address with area
            if ($foundItem->location_type === 'area' && $foundItem->area) {
                $similarity = 0;

                if ($reportedItem->location_address) {
                    $similarity = $this->calculateTextSimilarityWithContext(
                        $reportedItem->location_address,
                        $foundItem->area
                    );

                    // Consider landmarks if available
                    if ($foundItem->landmarks) {
                        $landmarkSimilarity = $this->calculateTextSimilarityWithContext(
                            $reportedItem->location_address,
                            $foundItem->landmarks
                        );
                        $similarity = max($similarity, $landmarkSimilarity * 0.7);
                    }
                }

                // Reduce confidence due to different location types
                return $similarity * 0.6;
            }

            return 0.1; // Return low similarity if found item has no valid location data
        }

        // If the reported item uses area-based location
        if ($reportedLocationType === 'area') {
            // Ensure we have the area for the reported item
            if (!$reportedItem->area) {
                return 0.1; // Return low similarity if area is missing
            }

            // If found item uses area
            if ($foundItem->location_type === 'area' && $foundItem->area) {
                $similarity = 0;
                $weights = [
                    'area' => 0.8,
                    'landmarks' => 0.2
                ];

                // Primary comparison based on area text
                $areaSimilarity = $this->calculateTextSimilarityWithContext(
                    $reportedItem->area,
                    $foundItem->area
                );
                $similarity += $areaSimilarity * $weights['area'];

                // Secondary comparison based on landmarks if available
                if ($reportedItem->landmarks && $foundItem->landmarks) {
                    $landmarkSimilarity = $this->calculateTextSimilarityWithContext(
                        $reportedItem->landmarks,
                        $foundItem->landmarks
                    );
                    $similarity += $landmarkSimilarity * $weights['landmarks'];
                }

                return $similarity;
            }

            // If found item uses map
            if ($foundItem->location_type === 'map' && $foundItem->location_address) {
                $similarity = 0;

                // Try to match the reported item's area with the found item's address
                $similarity = $this->calculateTextSimilarityWithContext(
                    $reportedItem->area,
                    $foundItem->location_address
                );

                // Consider landmarks as additional context if available
                if ($reportedItem->landmarks) {
                    $landmarkSimilarity = $this->calculateTextSimilarityWithContext(
                        $reportedItem->landmarks,
                        $foundItem->location_address
                    );
                    $similarity = max($similarity, $landmarkSimilarity * 0.7);
                }

                // Reduce confidence due to different location types
                return $similarity * 0.6;
            }

            return 0.1; // Return low similarity if found item has no valid location data
        }

        return 0.1; // Return low similarity for invalid location types
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
    public function calculateTextSimilarity($embedding1, $embedding2)
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

    /**
     * Calculate text similarity with context between two texts
     */
    public function calculateTextSimilarityWithContext($text1, $text2)
    {
        $embedding1 = $this->getTextEmbedding(['title' => '', 'description' => $text1]);
        $embedding2 = $this->getTextEmbedding(['title' => '', 'description' => $text2]);

        return $this->calculateTextSimilarity($embedding1, $embedding2);
    }

    /**
     * Calculate similarity between item attributes
     */
    protected function calculateAttributesSimilarity($item1, $item2)
    {
        $matchCount = 0;
        $totalAttributes = 0;

        // Check brand match
        if ($item1->brand && $item2->brand) {
            $totalAttributes++;
            if (strtolower($item1->brand) === strtolower($item2->brand)) {
                $matchCount++;
            }
        }

        // Check model match
        if ($item1->model && $item2->model) {
            $totalAttributes++;
            if (strtolower($item1->model) === strtolower($item2->model)) {
                $matchCount++;
            }
        }

        // Check color match
        if ($item1->color && $item2->color) {
            $totalAttributes++;
            if (strtolower($item1->color) === strtolower($item2->color)) {
                $matchCount++;
            }
        }

        // Check condition match
        if ($item1->condition && $item2->condition) {
            $totalAttributes++;
            if ($item1->condition === $item2->condition) {
                $matchCount++;
            }
        }

        return $totalAttributes > 0 ? $matchCount / $totalAttributes : 0;
    }

    /**
     * Pre-filters potential matches based on hard criteria before detailed similarity calculation
     * @param \App\Models\LostItem $reportedItem
     * @param Collection $foundItems
     * @return Collection
     */
    protected function preFilterCandidates($reportedItem, Collection $foundItems)
    {
        Log::info('Starting pre-filtering for reported item: ' . $reportedItem->id);

        return $foundItems->filter(function ($foundItem) use ($reportedItem) {
            // Don't match items from the same user
            if ($foundItem->user_id === $reportedItem->user_id) {
                Log::info("Filtered out item {$foundItem->id}: Same user");
                return false;
            }

            // Don't match items of the same type (lost with lost or found with found)
            if ($foundItem->item_type === $reportedItem->item_type) {
                Log::info("Filtered out item {$foundItem->id}: Same type");
                return false;
            }

            // Category matching - allow parent/child category matches
            $categoryMatch = $foundItem->category_id === $reportedItem->category_id ||
                            $foundItem->category->parent_id === $reportedItem->category->parent_id;
            if (!$categoryMatch) {
                Log::info("Filtered out item {$foundItem->id}: Category mismatch");
                return false;
            }

            // Time window - increased to 60 days
            $timeDiff = abs(Carbon::parse($foundItem->date)->diffInDays(Carbon::parse($reportedItem->date)));
            if ($timeDiff > 60) {
                Log::info("Filtered out item {$foundItem->id}: Time difference too large ({$timeDiff} days)");
                return false;
            }

            // Location proximity - increased to 20km
            if ($foundItem->latitude && $reportedItem->latitude) {
                $distance = $this->calculateDistance(
                    $reportedItem->latitude,
                    $reportedItem->longitude,
                    $foundItem->latitude,
                    $foundItem->longitude
                );
                if ($distance > 20) {
                    Log::info("Filtered out item {$foundItem->id}: Distance too large ({$distance}km)");
                    return false;
                }
            }

            Log::info("Item {$foundItem->id} passed all pre-filters");
            return true;
        });
    }
}
