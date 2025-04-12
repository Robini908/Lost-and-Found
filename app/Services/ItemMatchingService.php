<?php

namespace App\Services;

use GuzzleHttp\Client;
use App\Models\LostItem;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use App\Jobs\ProcessImageEmbeddings;
use Spatie\ImageOptimizer\OptimizerChainFactory;
use Illuminate\Support\Str;
use App\Models\ItemMatch;
use App\Events\ItemMatched;
use App\Notifications\ItemMatchedNotification;
use Illuminate\Support\Facades\Notification;

class ItemMatchingService
{
    protected $client;
    protected $apiToken;
    protected $imageOptimizer;

    public function __construct()
    {
        $this->client = new Client();
        $this->apiToken = config('services.huggingface.api_token');
        $this->imageOptimizer = OptimizerChainFactory::create();

        if (empty($this->apiToken)) {
            throw new \RuntimeException('HuggingFace API token not configured. Please check your HUGGINGFACE_API_TOKEN in .env');
        }
    }

    /**
     * Optimize and process image for embeddings
     *
     * @param string $imageUrl
     * @return string|null
     */
    protected function optimizeImage($imageUrl)
    {
        try {
            // Generate a unique filename for the optimized image
            $extension = pathinfo($imageUrl, PATHINFO_EXTENSION);
            $filename = Str::random(40) . '.' . $extension;
            $optimizedPath = storage_path('app/public/optimized/' . $filename);

            // Create directory if it doesn't exist
            if (!file_exists(dirname($optimizedPath))) {
                mkdir(dirname($optimizedPath), 0755, true);
            }

            // Download the image
            $imageContent = file_get_contents($imageUrl);
            if ($imageContent === false) {
                Log::error('Failed to download image', ['url' => $imageUrl]);
                return null;
            }

            // Save the original image
            file_put_contents($optimizedPath, $imageContent);

            // Optimize the image
            $this->imageOptimizer->optimize($optimizedPath);

            // Get the optimized image size
            $optimizedSize = filesize($optimizedPath);
            Log::info('Image optimized successfully', [
                'original_url' => $imageUrl,
                'optimized_size' => $optimizedSize,
                'optimized_path' => $optimizedPath
            ]);

            return $optimizedPath;
        } catch (\Exception $e) {
            Log::error('Image optimization failed', [
                'error' => $e->getMessage(),
                'url' => $imageUrl
            ]);
            return null;
        }
    }

    /**
     * Get embeddings for a given text using Hugging Face API.
     *
     * @param string $text
     * @return array|null
     */
    public function getTextEmbeddings($text)
    {
        if (empty($text)) {
            Log::warning('Empty text provided for embeddings');
            return null;
        }

        // Generate cache key based on text content
        $cacheKey = 'text_embedding_' . md5($text);

        // Try to get from cache first
        if (Cache::has($cacheKey)) {
            Log::info('Retrieved text embeddings from cache', ['cache_key' => $cacheKey]);
            return Cache::get($cacheKey);
        }

        Log::info('Starting text embedding process', [
            'text_length' => strlen($text),
            'model_endpoint' => config('services.huggingface.text_model_endpoint')
        ]);

        $maxRetries = config('services.huggingface.max_retries', 3);
        $retryDelay = config('services.huggingface.retry_delay', 1000);

        // Log the token format (masked)
        $maskedToken = substr_replace($this->apiToken, '...', 10, -5);
        Log::debug('API Token format check', [
            'token_length' => strlen($this->apiToken),
            'masked_token' => $maskedToken,
            'starts_with_hf' => str_starts_with($this->apiToken, 'hf_')
        ]);

        for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
            try {
                Log::debug("Attempt $attempt: Sending request to HuggingFace API", [
                    'attempt' => $attempt,
                    'max_retries' => $maxRetries
                ]);

                $requestData = [
                    'headers' => [
                        'Authorization' => 'Bearer hf_' . ltrim($this->apiToken, 'hf_'),
                        'Content-Type' => 'application/json',
                    ],
                    'json' => [
                        'inputs' => $text,
                        'options' => ['wait_for_model' => true]
                    ]
                ];

                $response = $this->client->post(
                    config('services.huggingface.text_model_endpoint'),
                    $requestData
                );

                $embeddings = json_decode($response->getBody(), true);

                if ($embeddings) {
                    // Cache the embeddings for future use (1 week)
                    Cache::put($cacheKey, $embeddings, now()->addWeek());
                    return $embeddings;
                }

                return null;

            } catch (\Exception $e) {
                Log::warning("Failed to get text embeddings (attempt $attempt)", [
                    'error' => $e->getMessage()
                ]);

                if ($attempt < $maxRetries) {
                    $delayMs = $retryDelay * $attempt; // Exponential backoff
                    Log::info("Waiting {$delayMs}ms before next retry");
                    usleep($delayMs * 1000);
                } else {
                    Log::error('All attempts to fetch text embeddings failed', [
                        'total_attempts' => $maxRetries,
                        'final_error' => $e->getMessage()
                    ]);
                    return null;
                }
            }
        }
        return null;
    }

    /**
     * Get embeddings for multiple texts in batch
     *
     * @param array $texts
     * @return array
     */
    public function getBatchTextEmbeddings(array $texts)
    {
        $results = [];
        $textsToProcess = [];
        $cacheKeys = [];

        // Check cache first for each text
        foreach ($texts as $index => $text) {
            // Clean and normalize the text
            $text = trim($text);
            $cacheKey = 'text_embedding_' . md5($text);
            if (Cache::has($cacheKey)) {
                $results[$index] = Cache::get($cacheKey);
            } else {
                $textsToProcess[$index] = $text;
                $cacheKeys[$index] = $cacheKey;
            }
        }

        // If all results were in cache, return early
        if (empty($textsToProcess)) {
            return $results;
        }

        try {
            $response = $this->client->post(
                config('services.huggingface.text_model_endpoint'),
                [
                    'headers' => [
                        'Authorization' => 'Bearer hf_' . ltrim($this->apiToken, 'hf_'),
                        'Content-Type' => 'application/json',
                    ],
                    'json' => [
                        'inputs' => array_values($textsToProcess),
                        'options' => ['wait_for_model' => true]
                    ]
                ]
            );

            $batchEmbeddings = json_decode($response->getBody(), true);

            if ($batchEmbeddings) {
                foreach ($textsToProcess as $index => $text) {
                    $embedding = $batchEmbeddings[array_search($index, array_keys($textsToProcess))];
                    $results[$index] = $embedding;
                    Cache::put($cacheKeys[$index], $embedding, now()->addWeek());
                }
            }
        } catch (\Exception $e) {
            Log::error('Batch text embeddings failed', [
                'error' => $e->getMessage(),
                'count' => count($textsToProcess)
            ]);
        }

        return $results;
    }

    protected function getImageEmbeddings($imageUrl)
    {
        if (empty($imageUrl)) {
            Log::warning('Empty image URL provided for embeddings');
            return null;
        }

        // Extract image ID from URL
        preg_match('/\/([^\/]+)\.[^\.]+$/', $imageUrl, $matches);
        $imageId = $matches[1] ?? md5($imageUrl);

        $cacheKey = 'image_embedding_' . $imageId;

        // Try to get embeddings from cache first
        if (Cache::has($cacheKey)) {
            Log::info('Retrieved image embeddings from cache', ['image_id' => $imageId]);
            return Cache::get($cacheKey);
        }

        // Optimize the image first
        $optimizedImagePath = $this->optimizeImage($imageUrl);
        if (!$optimizedImagePath) {
            Log::error('Image optimization failed, cannot proceed with embeddings');
            return null;
        }

        try {
            // Process optimized image embeddings
            $job = new ProcessImageEmbeddings($optimizedImagePath, $imageId);
            $embeddings = $job->handle();

            // Clean up the optimized image
            @unlink($optimizedImagePath);

            if ($embeddings) {
                Cache::put($cacheKey, $embeddings, now()->addWeek());
            }

            return $embeddings;
        } catch (\Exception $e) {
            Log::error('Failed to process image embeddings', [
                'error' => $e->getMessage(),
                'image_id' => $imageId
            ]);
            // Clean up the optimized image in case of error
            @unlink($optimizedImagePath);
            return null;
        }
    }

    /**
     * Calculate cosine similarity between two embeddings.
     *
     * @param array|null $embeddingA
     * @param array|null $embeddingB
     * @return float
     */
    public function cosineSimilarity($embeddingA, $embeddingB)
    {
        if (!is_array($embeddingA) || !is_array($embeddingB)) {
            Log::error('Invalid embeddings provided for similarity calculation', [
                'embeddingA' => gettype($embeddingA),
                'embeddingB' => gettype($embeddingB)
            ]);
            return 0.0;
        }

        try {
            $dotProduct = 0;
            $normA = 0;
            $normB = 0;

            foreach ($embeddingA as $i => $value) {
                if (!isset($embeddingB[$i])) {
                    Log::error('Embeddings have different dimensions');
                    return 0.0;
                }
                $dotProduct += $value * $embeddingB[$i];
                $normA += $value * $value;
                $normB += $embeddingB[$i] * $embeddingB[$i];
            }

            $normA = sqrt($normA);
            $normB = sqrt($normB);

            if ($normA == 0 || $normB == 0) {
                return 0.0;
            }

            return $dotProduct / ($normA * $normB);
        } catch (\Exception $e) {
            Log::error('Error calculating cosine similarity: ' . $e->getMessage());
            return 0.0;
        }
    }

    /**
     * Calculate the overall similarity score considering text, image, category, location, and date.
     *
     * @param LostItem $lostItem
     * @param LostItem $foundItem
     * @return float
     */
    protected function calculateOverallSimilarity($lostItem, $foundItem)
    {
        Log::info('Starting similarity calculation', [
            'lost_item_id' => $lostItem->id,
            'found_item_id' => $foundItem->id
        ]);

        // Try Hugging Face embeddings first
        $textEmbeddingsLost = $this->getTextEmbeddings($lostItem->description);
        $textEmbeddingsFound = $this->getTextEmbeddings($foundItem->description);

        Log::debug('Text embeddings status', [
            'lost_item_embeddings' => $textEmbeddingsLost ? 'generated' : 'failed',
            'found_item_embeddings' => $textEmbeddingsFound ? 'generated' : 'failed'
        ]);

        $textSimilarity = $this->cosineSimilarity($textEmbeddingsLost, $textEmbeddingsFound);

        $imageSimilarity = 0;
        if ($lostItem->images->isNotEmpty() && $foundItem->images->isNotEmpty()) {
            Log::info('Processing image similarity', [
                'lost_item_image' => $lostItem->images->first()->url,
                'found_item_image' => $foundItem->images->first()->url
            ]);

            $lostImageEmbedding = $this->getImageEmbeddings($lostItem->images->first()->url);
            $foundImageEmbedding = $this->getImageEmbeddings($foundItem->images->first()->url);

            Log::debug('Image embeddings status', [
                'lost_image_embeddings' => $lostImageEmbedding ? 'generated' : 'failed',
                'found_image_embeddings' => $foundImageEmbedding ? 'generated' : 'failed'
            ]);

            if ($lostImageEmbedding && $foundImageEmbedding) {
                $imageSimilarity = $this->cosineSimilarity($lostImageEmbedding, $foundImageEmbedding);
            }
        }

        // Fallback to keyword-based similarity if embeddings fail
        if ($textSimilarity === null || $imageSimilarity === null) {
            Log::info('Falling back to basic similarity methods', [
                'reason' => 'Embedding generation failed',
                'text_similarity_failed' => $textSimilarity === null,
                'image_similarity_failed' => $imageSimilarity === null
            ]);

            $textSimilarity = $this->fallbackTextSimilarity($lostItem->description, $foundItem->description);
            $imageSimilarity = $this->fallbackImageSimilarity($lostItem->images, $foundItem->images);
        }

        $categorySimilarity = $lostItem->category_id === $foundItem->category_id ? 1 : 0;
        $locationSimilarity = $this->calculateLocationSimilarity($lostItem, $foundItem);
        $dateSimilarity = $this->calculateDateSimilarity($lostItem, $foundItem);

        $overallSimilarity = (
            $textSimilarity * 0.4 +
            $imageSimilarity * 0.3 +
            $categorySimilarity * 0.1 +
            $locationSimilarity * 0.1 +
            $dateSimilarity * 0.1
        );

        Log::info('Similarity calculation complete', [
            'text_similarity' => $textSimilarity,
            'image_similarity' => $imageSimilarity,
            'category_similarity' => $categorySimilarity,
            'location_similarity' => $locationSimilarity,
            'date_similarity' => $dateSimilarity,
            'overall_similarity' => $overallSimilarity
        ]);

        return $overallSimilarity;
    }

    protected function fallbackTextSimilarity($textA, $textB)
    {
        // Simple keyword-based similarity
        $wordsA = array_unique(str_word_count(strtolower($textA), 1));
        $wordsB = array_unique(str_word_count(strtolower($textB), 1));
        $commonWords = array_intersect($wordsA, $wordsB);
        $similarity = count($commonWords) / max(count($wordsA), count($wordsB));
        return $similarity;
    }

    protected function fallbackImageSimilarity($imagesA, $imagesB)
    {
        // Compare basic metadata (e.g., file size, dimensions)
        if ($imagesA->isEmpty() || $imagesB->isEmpty()) {
            return 0;
        }

        $imageA = $imagesA->first();
        $imageB = $imagesB->first();

        $similarity = 0;
        if ($imageA->size === $imageB->size) {
            $similarity += 0.5;
        }
        if ($imageA->width === $imageB->width && $imageA->height === $imageB->height) {
            $similarity += 0.5;
        }

        return $similarity;
    }

    /**
     * Calculate location similarity based on geolocation.
     *
     * @param LostItem $lostItem
     * @param LostItem $foundItem
     * @return float
     */
    protected function calculateLocationSimilarity($lostItem, $foundItem)
    {
        if (!$lostItem->geolocation || !$foundItem->geolocation) {
            return 0;
        }

        $lat1 = $lostItem->geolocation['lat'];
        $lng1 = $lostItem->geolocation['lng'];
        $lat2 = $foundItem->geolocation['lat'];
        $lng2 = $foundItem->geolocation['lng'];

        $distance = $this->haversineDistance($lat1, $lng1, $lat2, $lng2);

        // Normalize distance to a similarity score (0-1)
        $maxDistance = 1000; // 1 km
        return max(0, 1 - ($distance / $maxDistance));
    }

    /**
     * Calculate the Haversine distance between two points.
     *
     * @param float $lat1
     * @param float $lng1
     * @param float $lat2
     * @param float $lng2
     * @return float
     */
    protected function haversineDistance($lat1, $lng1, $lat2, $lng2)
    {
        $earthRadius = 6371000; // in meters

        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLng / 2) * sin($dLng / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    /**
     * Calculate date similarity based on the date lost and date found.
     *
     * @param LostItem $lostItem
     * @param LostItem $foundItem
     * @return float
     */
    protected function calculateDateSimilarity($lostItem, $foundItem)
    {
        if (!$lostItem->date_lost || !$foundItem->date_found) {
            return 0;
        }

        $dateLost = $lostItem->date_lost;
        $dateFound = $foundItem->date_found;

        $daysDifference = abs($dateLost->diffInDays($dateFound));

        // Normalize date difference to a similarity score (0-1)
        $maxDays = 30; // 1 month
        return max(0, 1 - ($daysDifference / $maxDays));
    }

    /**
     * Find potential matches for a given lost item.
     *
     * @param LostItem $lostItem
     * @return \Illuminate\Support\Collection
     */
    public function findMatches(LostItem $lostItem)
    {
        Log::info('Starting match finding process', ['item_id' => $lostItem->id]);

        $foundItems = LostItem::where('item_type', LostItem::TYPE_FOUND)
            ->where('user_id', '!=', $lostItem->user_id)
            ->with(['images', 'category']) // Eager load relationships
            ->get();

        if ($foundItems->isEmpty()) {
            return collect();
        }

        // Pre-calculate text embeddings for lost item
        $lostItemTextEmbeddings = $this->getTextEmbeddings($lostItem->description);
        $lostItemImageEmbeddings = null;

        if ($lostItem->images->isNotEmpty()) {
            $lostItemImageEmbeddings = $this->getImageEmbeddings($lostItem->images->first()->url);
        }

        // Process items in chunks for better memory management
        $chunks = $foundItems->chunk(5);
        $matches = collect();

        foreach ($chunks as $chunk) {
            $chunkMatches = $this->processItemChunk($chunk, $lostItem, $lostItemTextEmbeddings, $lostItemImageEmbeddings);
            $matches = $matches->concat($chunkMatches);
        }

        return $matches->sortByDesc('similarity');
    }

    protected function processItemChunk($items, $lostItem, $lostItemTextEmbeddings, $lostItemImageEmbeddings)
    {
        $matches = collect();
        $chunkStartTime = microtime(true);

        Log::debug('Processing item chunk', [
            'chunk_size' => $items->count(),
            'lost_item_id' => $lostItem->id,
            'has_text_embeddings' => !empty($lostItemTextEmbeddings),
            'has_image_embeddings' => !empty($lostItemImageEmbeddings)
        ]);

        foreach ($items as $foundItem) {
            $itemStartTime = microtime(true);
            try {
                $similarity = $this->calculateOptimizedSimilarity(
                    $lostItem,
                    $foundItem,
                    $lostItemTextEmbeddings,
                    $lostItemImageEmbeddings
                );

                $processingTime = (microtime(true) - $itemStartTime) * 1000; // Convert to milliseconds

                Log::debug('Item matching result', [
                    'lost_item_id' => $lostItem->id,
                    'found_item_id' => $foundItem->id,
                    'similarity_score' => $similarity,
                    'processing_time_ms' => round($processingTime, 2),
                    'matched' => $similarity > 0.3
                ]);

                if ($similarity > 0.3) {
                    // Create or update the match in the database
                    $match = ItemMatch::updateOrCreate(
                        [
                            'lost_item_id' => $lostItem->id,
                            'found_item_id' => $foundItem->id
                        ],
                        [
                            'similarity_score' => $similarity,
                            'matched_at' => now(),
                            'processing_time_ms' => $processingTime
                        ]
                    );

                    $matches->push([
                        'found_item' => $foundItem,
                        'similarity' => $similarity,
                        'processing_time_ms' => $processingTime
                    ]);

                    // Send notifications to both users
                    try {
                        // Notify the person who reported the lost item
                        $lostItem->user->notify(new ItemMatchedNotification($match, 'reporter'));

                        // Notify the person who found the item
                        $foundItem->user->notify(new ItemMatchedNotification($match, 'finder'));

                        Log::info('Match notifications sent successfully', [
                            'match_id' => $match->id,
                            'lost_item_user' => $lostItem->user->id,
                            'found_item_user' => $foundItem->user->id
                        ]);
                    } catch (\Exception $e) {
                        Log::error('Failed to send match notifications', [
                            'match_id' => $match->id,
                            'error' => $e->getMessage()
                        ]);
                    }

                    // Broadcast a real-time event for the match
                    broadcast(new ItemMatched([
                        'lost_item_id' => $lostItem->id,
                        'found_item_id' => $foundItem->id,
                        'similarity_score' => $similarity,
                        'processing_time_ms' => $processingTime
                    ]));
                }
            } catch (\Exception $e) {
                Log::error('Error processing item in chunk', [
                    'lost_item_id' => $lostItem->id,
                    'found_item_id' => $foundItem->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                continue;
            }
        }

        $totalChunkTime = microtime(true) - $chunkStartTime;
        Log::info('Chunk processing complete', [
            'chunk_size' => $items->count(),
            'matches_found' => $matches->count(),
            'total_processing_time_ms' => round($totalChunkTime * 1000, 2),
            'average_time_per_item_ms' => round(($totalChunkTime / $items->count()) * 1000, 2)
        ]);

        return $matches;
    }

    protected function calculateOptimizedSimilarity($lostItem, $foundItem, $lostItemTextEmbeddings, $lostItemImageEmbeddings)
    {
        Log::debug('Starting similarity calculation', [
            'lost_item' => [
                'id' => $lostItem->id,
                'title' => $lostItem->title,
                'category_id' => $lostItem->category_id,
                'has_images' => $lostItem->images->isNotEmpty()
            ],
            'found_item' => [
                'id' => $foundItem->id,
                'title' => $foundItem->title,
                'category_id' => $foundItem->category_id,
                'has_images' => $foundItem->images->isNotEmpty()
            ]
        ]);

        // Quick checks first
        $categorySimilarity = $lostItem->category_id === $foundItem->category_id ? 1 : 0;
        Log::debug('Category similarity check', [
            'category_similarity' => $categorySimilarity,
            'lost_category' => $lostItem->category_id,
            'found_category' => $foundItem->category_id
        ]);

        // If categories don't match and it's required, return early
        if ($categorySimilarity === 0 && config('matching.require_same_category', false)) {
            Log::debug('Early return: Categories don\'t match and same category is required');
            return 0;
        }

        // Calculate text similarity
        $foundItemTextEmbeddings = $this->getTextEmbeddings($foundItem->description);
        $textSimilarity = $this->cosineSimilarity($lostItemTextEmbeddings, $foundItemTextEmbeddings);

        Log::debug('Text similarity calculation', [
            'text_similarity' => $textSimilarity,
            'lost_description_length' => strlen($lostItem->description),
            'found_description_length' => strlen($foundItem->description),
            'has_lost_embeddings' => !empty($lostItemTextEmbeddings),
            'has_found_embeddings' => !empty($foundItemTextEmbeddings)
        ]);

        // If text similarity is too low, return early
        if ($textSimilarity < 0.3) {
            Log::debug('Early return: Text similarity too low', ['text_similarity' => $textSimilarity]);
            return $textSimilarity;
        }

        // Comment out image similarity calculation
        /*
        $imageSimilarity = 0;
        if ($lostItem->images->isNotEmpty() && $foundItem->images->isNotEmpty()) {
            $foundItemImageEmbeddings = $this->getImageEmbeddings($foundItem->images->first()->url);
            if ($lostItemImageEmbeddings && $foundItemImageEmbeddings) {
                $imageSimilarity = $this->cosineSimilarity($lostItemImageEmbeddings, $foundItemImageEmbeddings);
                Log::debug('Image similarity calculation', [
                    'image_similarity' => $imageSimilarity,
                    'lost_image_url' => $lostItem->images->first()->url,
                    'found_image_url' => $foundItem->images->first()->url,
                    'has_lost_image_embeddings' => !empty($lostItemImageEmbeddings),
                    'has_found_image_embeddings' => !empty($foundItemImageEmbeddings)
                ]);
            }
        }
        */
        $imageSimilarity = 0; // Set to 0 since we're not using image similarity

        // Calculate other similarities
        $locationSimilarity = $this->calculateLocationSimilarity($lostItem, $foundItem);
        $dateSimilarity = $this->calculateDateSimilarity($lostItem, $foundItem);

        Log::debug('Additional similarity calculations', [
            'location_similarity' => $locationSimilarity,
            'date_similarity' => $dateSimilarity,
            'lost_date' => $lostItem->date_lost,
            'found_date' => $foundItem->date_found
        ]);

        // Adjust weights since we're not using image similarity
        $overallSimilarity = (
            $textSimilarity * 0.6 + // Increased from 0.4
            // imageSimilarity * 0.3 + // Commented out
            $categorySimilarity * 0.15 + // Increased from 0.1
            $locationSimilarity * 0.15 + // Increased from 0.1
            $dateSimilarity * 0.1
        );

        Log::info('Final similarity score', [
            'lost_item_id' => $lostItem->id,
            'found_item_id' => $foundItem->id,
            'overall_similarity' => $overallSimilarity,
            'components' => [
                'text' => $textSimilarity * 0.6,
                //'image' => $imageSimilarity * 0.3, // Commented out
                'category' => $categorySimilarity * 0.15,
                'location' => $locationSimilarity * 0.15,
                'date' => $dateSimilarity * 0.1
            ]
        ]);

        return $overallSimilarity;
    }

    /**
     * Check if a lost item has any matches.
     *
     * @param LostItem $lostItem
     * @return bool
     */
    public function hasMatches(LostItem $lostItem)
    {
        try {
            $foundItems = LostItem::where('item_type', LostItem::TYPE_FOUND)
                ->where('user_id', '!=', $lostItem->user_id)
                ->where('category_id', $lostItem->category_id) // Quick filter by category first
                ->with(['images', 'category'])
                ->limit(10) // Limit initial search
                ->get();

            if ($foundItems->isEmpty()) {
                return false;
            }

            // Pre-calculate embeddings
            $lostItemTextEmbeddings = $this->getTextEmbeddings($lostItem->description);
            $lostItemImageEmbeddings = null;

            if ($lostItem->images->isNotEmpty()) {
                $lostItemImageEmbeddings = $this->getImageEmbeddings($lostItem->images->first()->url);
            }

            foreach ($foundItems as $foundItem) {
                try {
                    $similarity = $this->calculateOptimizedSimilarity(
                        $lostItem,
                        $foundItem,
                        $lostItemTextEmbeddings,
                        $lostItemImageEmbeddings
                    );

                    if ($similarity > 0.3) {
                        return true;
                    }
                } catch (\Exception $e) {
                    Log::error('Error in hasMatches similarity calculation', [
                        'lost_item_id' => $lostItem->id,
                        'found_item_id' => $foundItem->id,
                        'error' => $e->getMessage()
                    ]);
                    continue;
                }
            }

            return false;
        } catch (\Exception $e) {
            Log::error('Error in hasMatches', [
                'lost_item_id' => $lostItem->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Get category suggestions with database first and AI as fallback
     *
     * @param string $title
     * @param \Illuminate\Database\Eloquent\Collection $categories
     * @return array
     */
    public function getCategorySuggestions($title, $categories)
    {
        if (strlen($title) < 3) {
            return [];
        }

        try {
            // Step 1: Try exact matches from database first (fastest)
            $directMatches = $this->getDirectCategorySuggestions($title, $categories);
            if (!empty($directMatches)) {
                Log::info('Found direct category matches', [
                    'title' => $title,
                    'matches' => count($directMatches)
                ]);
                return $directMatches;
            }

            // Step 2: Try word-based matches (still fast)
            $wordMatches = $this->getWordBasedCategorySuggestions($title, $categories);
            if (!empty($wordMatches)) {
                Log::info('Found word-based category matches', [
                    'title' => $title,
                    'matches' => count($wordMatches)
                ]);
                return $wordMatches;
            }

            // Step 3: Try semantic fallback matching (medium speed)
            $semanticMatches = $this->getSemanticCategorySuggestions($title, $categories);
            if (!empty($semanticMatches)) {
                Log::info('Found semantic category matches', [
                    'title' => $title,
                    'matches' => count($semanticMatches)
                ]);
                return $semanticMatches;
            }

            // Step 4: Only use AI as final fallback (slowest)
            Log::info('No database matches found, falling back to AI suggestions', [
                'title' => $title
            ]);
            return $this->getAIBasedCategorySuggestions($title, $categories);
        } catch (\Exception $e) {
            Log::error('Category matching failed, falling back to simple matching', [
                'error' => $e->getMessage(),
                'title' => $title
            ]);
            return $this->getFallbackCategorySuggestions($title, $categories);
        }
    }

    /**
     * Get direct category matches based on exact text matching
     *
     * @param string $title
     * @param \Illuminate\Database\Eloquent\Collection $categories
     * @return array
     */
    public function getDirectCategorySuggestions($title, $categories)
    {
        $titleLower = strtolower(trim($title));

        // Step 1: Check for exact name matches (priority 1)
        $exactMatches = $categories->filter(function ($category) use ($titleLower) {
            return strtolower($category->name) === $titleLower;
        });

        if (!$exactMatches->isEmpty()) {
            return $exactMatches->take(3)->pluck('id')->toArray();
        }

        // Step 2: Check for substring matches (priority 2)
        $substringMatches = $categories->filter(function ($category) use ($titleLower) {
            $categoryLower = strtolower($category->name);
            return strpos($categoryLower, $titleLower) !== false ||
                   strpos($titleLower, $categoryLower) !== false;
        });

        if (!$substringMatches->isEmpty()) {
            return $substringMatches->take(3)->pluck('id')->toArray();
        }

        return [];
    }

    /**
     * Get category matches based on word overlap
     *
     * @param string $title
     * @param \Illuminate\Database\Eloquent\Collection $categories
     * @return array
     */
    public function getWordBasedCategorySuggestions($title, $categories)
    {
        $titleWords = explode(' ', strtolower(trim($title)));
        // Filter out common words
        $stopWords = ['the', 'a', 'an', 'my', 'our', 'this', 'that', 'lost', 'found', 'item'];
        $titleWords = array_diff($titleWords, $stopWords);

        if (empty($titleWords)) {
            return [];
        }

        $matchScores = [];

        foreach ($categories as $category) {
            $categoryWords = explode(' ', strtolower($category->name));
            $categoryWords = array_diff($categoryWords, $stopWords);

            $matchingWords = array_intersect($titleWords, $categoryWords);

            if (!empty($matchingWords)) {
                // Calculate match score based on number of matching words and their importance
                $score = count($matchingWords) / max(count($titleWords), 1);
                $matchScores[$category->id] = $score;
            }
        }

        // Sort by score and return top matches
        if (!empty($matchScores)) {
            arsort($matchScores);
            return array_slice(array_keys($matchScores), 0, 3);
        }

        return [];
    }

    /**
     * Get semantic category suggestions based on predefined semantic groups
     *
     * @param string $title
     * @param \Illuminate\Database\Eloquent\Collection $categories
     * @return array
     */
    public function getSemanticCategorySuggestions($title, $categories)
    {
        // Use the existing semantic groups from isSemanticCategoryMatch
        $semanticMatches = $this->findSemanticMatches($title, $categories);

        if (!empty($semanticMatches)) {
            return array_slice($semanticMatches, 0, 3);
        }

        return [];
    }

    /**
     * Get AI-based category suggestions using embeddings
     *
     * @param string $title
     * @param \Illuminate\Database\Eloquent\Collection $categories
     * @return array
     */
    public function getAIBasedCategorySuggestions($title, $categories)
    {
        try {
            // Step 1: Enrich the search context with semantic information
            $searchContext = $this->buildSearchContext($title);

            // Step 2: Get embeddings for the enriched search context
            $searchEmbedding = $this->getTextEmbeddings($searchContext);
            if (!$searchEmbedding) {
                throw new \Exception('Failed to generate embeddings for search context');
            }

            // Step 3: Prepare category contexts and get their embeddings
            $categoryEmbeddings = [];
            $categoryContexts = [];

            foreach ($categories as $category) {
                $categoryContext = $this->buildEnrichedCategoryContext($category);
                $categoryContexts[$category->id] = $categoryContext;

                $embedding = $this->getTextEmbeddings($categoryContext);
                if ($embedding) {
                    $categoryEmbeddings[$category->id] = $embedding;
                }
            }

            // Step 4: Calculate semantic similarities with enhanced confidence scoring
        $similarities = [];
            $confidenceThreshold = 0.30; // Lower threshold to catch more semantic matches

            foreach ($categoryEmbeddings as $categoryId => $categoryEmbedding) {
                $similarity = $this->cosineSimilarity($searchEmbedding, $categoryEmbedding);

                // Enhanced semantic confidence calculation
                $confidence = $this->calculateSemanticConfidence(
                    $title,
                    $searchContext,
                    $categoryContexts[$categoryId],
                    $similarity
                );

                // Boost confidence for semantic category matches
                if ($this->isSemanticCategoryMatch($title, $categories[$categoryId])) {
                    $confidence += 0.2;
                }

                $confidence = min(1.0, $confidence); // Cap at 1.0

                if ($confidence >= $confidenceThreshold) {
                    $similarities[$categoryId] = $confidence;
                }
            }

            // Step 5: Sort by confidence and get top matches
            arsort($similarities);
            $topMatches = array_slice(array_keys($similarities), 0, 3);

            // If no good matches, try semantic matching
            if (empty($topMatches)) {
                $semanticMatches = $this->findSemanticMatches($title, $categories);
                if (!empty($semanticMatches)) {
                    $topMatches = array_slice($semanticMatches, 0, 3);
                }
            }

            return $topMatches;

        } catch (\Exception $e) {
            Log::error('AI-based category suggestion failed', [
                'error' => $e->getMessage(),
                'title' => $title
            ]);
            return []; // Don't fall back to simple matching
        }
    }

    /**
     * Check if there's a semantic match between item and category
     */
    protected function isSemanticCategoryMatch($title, $category)
    {
        $semanticGroups = [
            'clothing' => ['jacket', 'shirt', 'pants', 'dress', 'coat', 'sweater', 'hoodie', 'wear', 'clothes'],
            'electronics' => ['phone', 'laptop', 'tablet', 'computer', 'device', 'electronic', 'gadget'],
            'accessories' => ['wallet', 'bag', 'purse', 'backpack', 'watch', 'accessory'],
            'documents' => ['passport', 'id', 'license', 'certificate', 'card', 'document'],
            'valuables' => ['jewelry', 'ring', 'necklace', 'gold', 'silver', 'precious']
        ];

        $titleLower = strtolower($title);
        $categoryLower = strtolower($category->name);

        foreach ($semanticGroups as $group => $keywords) {
            $titleMatch = false;
            $categoryMatch = false;

            foreach ($keywords as $keyword) {
                if (strpos($titleLower, $keyword) !== false) {
                    $titleMatch = true;
                }
                if (strpos($categoryLower, $keyword) !== false) {
                    $categoryMatch = true;
                }
            }

            if ($titleMatch && $categoryMatch) {
                return true;
            }
        }

        return false;
    }

    /**
     * Find semantic matches based on item type and category relationships
     */
    protected function findSemanticMatches($title, $categories)
    {
        $matches = [];
        $titleLower = strtolower($title);

        // Define semantic relationships
        $relationships = [
            ['keywords' => ['jacket', 'coat', 'sweater', 'hoodie'], 'category' => 'clothing'],
            ['keywords' => ['phone', 'laptop', 'tablet'], 'category' => 'electronics'],
            ['keywords' => ['wallet', 'bag', 'purse'], 'category' => 'accessories'],
            ['keywords' => ['passport', 'id', 'license'], 'category' => 'documents'],
            ['keywords' => ['jewelry', 'ring', 'necklace'], 'category' => 'valuables']
        ];

        foreach ($relationships as $rel) {
            foreach ($rel['keywords'] as $keyword) {
                if (strpos($titleLower, $keyword) !== false) {
                    // Find categories that match the semantic category
        foreach ($categories as $category) {
                        if (strpos(strtolower($category->name), $rel['category']) !== false) {
                            $matches[] = $category->id;
                        }
                    }
                    break;
                }
            }
        }

        return array_unique($matches);
    }

    /**
     * Build category hierarchy for better semantic understanding
     */
    protected function buildCategoryHierarchy($categories)
    {
        $hierarchy = [];
        $categoryGroups = [
            'clothing' => ['jacket', 'shirt', 'pants', 'dress', 'coat', 'sweater', 'hoodie', 'clothing'],
            'electronics' => ['phone', 'laptop', 'tablet', 'computer', 'device', 'gadget'],
            'accessories' => ['wallet', 'bag', 'purse', 'backpack', 'watch'],
            'documents' => ['passport', 'id', 'license', 'certificate', 'card'],
            'valuables' => ['jewelry', 'ring', 'necklace', 'gold', 'silver']
        ];

        foreach ($categories as $category) {
            $categoryName = strtolower($category->name);
            foreach ($categoryGroups as $group => $keywords) {
                foreach ($keywords as $keyword) {
                    if (strpos($categoryName, $keyword) !== false) {
                        $hierarchy[$category->id] = $group;
                        break 2;
                    }
                }
            }
        }

        return $hierarchy;
    }

    /**
     * Calculate enhanced semantic confidence with contextual understanding
     */
    protected function calculateSemanticConfidence($title, $searchContext, $categoryContext, $baseSimilarity)
    {
        $confidence = $baseSimilarity;

        // Enhanced semantic analysis
        $relevanceFactors = [
            'direct_match' => $this->checkDirectMatch($title, $categoryContext),
            'context_overlap' => $this->analyzeContextOverlap($searchContext, $categoryContext),
            'semantic_coherence' => $this->analyzeSemanticCoherence($title, $categoryContext)
        ];

        // Apply weighted boosts with semantic priority
        $weights = [
            'direct_match' => 0.2,
            'context_overlap' => 0.3,
            'semantic_coherence' => 0.5
        ];

        foreach ($relevanceFactors as $factor => $value) {
            $confidence += $value * $weights[$factor];
        }

        return min(1.0, $confidence);
    }

    /**
     * Build rich semantic search context for item categorization
     */
    public function buildSearchContext($title)
    {
        $context = "Item Analysis Context:\n";
        $context .= "Title: $title\n";

        // Add semantic markers for better embedding understanding
        $context .= "Purpose: Categorize this item based on its characteristics, usage, and context.\n";

        // Add potential item attributes
        $attributes = $this->extractItemAttributes($title);
        if (!empty($attributes)) {
            $context .= "Attributes: " . implode(", ", $attributes) . "\n";
        }

        return $context;
    }

    /**
     * Build enriched category context with semantic understanding
     */
    public function buildEnrichedCategoryContext($category)
    {
        $context = "Category Analysis:\n";
        $context .= "Name: {$category->name}\n";

        if ($category->description) {
            $context .= "Description: {$category->description}\n";
        }

        // Add semantic markers for category understanding
        $context .= "Purpose: This category is designed for items that are ";
        $context .= $this->expandCategoryContext($category->name);

        return $context;
    }

    /**
     * Extract meaningful attributes from item title
     */
    public function extractItemAttributes($title)
    {
        $attributes = [];

        // Common material patterns
        $materials = ['leather', 'metal', 'plastic', 'wood', 'glass', 'fabric'];
        foreach ($materials as $material) {
            if (stripos($title, $material) !== false) {
                $attributes[] = "Material: $material";
            }
        }

        // Item type patterns
        $types = [
            'electronic' => ['phone', 'laptop', 'tablet', 'device'],
            'document' => ['passport', 'id', 'card', 'certificate'],
            'accessory' => ['wallet', 'bag', 'purse', 'watch'],
            'valuable' => ['jewelry', 'ring', 'necklace', 'gold', 'silver']
        ];

        foreach ($types as $category => $keywords) {
            foreach ($keywords as $keyword) {
                if (stripos($title, $keyword) !== false) {
                    $attributes[] = "Type: $category";
                break;
                }
            }
        }

        return array_unique($attributes);
    }

    /**
     * Expand category context with semantic understanding
     */
    public function expandCategoryContext($categoryName)
    {
        $categoryContexts = [
            'electronics' => 'electronic devices, gadgets, and technological items that may have significant personal and monetary value',
            'documents' => 'important papers, identification items, and official records that are crucial for personal identity and legal matters',
            'accessories' => 'personal items worn or carried daily that often have both practical and sentimental value',
            'jewelry' => 'precious items of personal adornment that often carry significant emotional and monetary worth',
            'clothing' => 'personal garments and attire that may have both practical and sentimental importance',
            'bags' => 'containers and carriers that often hold other valuable items and personal belongings',
            'keys' => 'access tools and security items that are critical for daily life and security',
            'books' => 'reading materials and educational resources that may have academic or personal significance',
            'misc' => 'unique items with specific personal significance that may not fit traditional categories'
        ];

        foreach ($categoryContexts as $key => $context) {
            if (stripos($categoryName, $key) !== false) {
        return $context;
            }
        }

        return 'items with specific characteristics and purposes that require careful categorization';
    }

    /**
     * Suggest a new category based on item analysis
     */
    public function suggestNewCategory($title, $searchContext)
    {
        // Extract key characteristics
        $attributes = $this->extractItemAttributes($title);

        // Generate category suggestion
        $suggestion = [
            'name' => $this->generateCategoryName($title, $attributes),
            'description' => $this->generateCategoryDescription($title, $searchContext, $attributes),
            'confidence' => 'AI_GENERATED'
        ];

        return $suggestion;
    }

    /**
     * Generate an appropriate category name
     */
    public function generateCategoryName($title, $attributes)
    {
        // Remove common words and clean the title
        $words = explode(' ', strtolower($title));
        $commonWords = ['the', 'a', 'an', 'my', 'lost', 'found'];
        $words = array_diff($words, $commonWords);

        // Use the most significant terms
        $significant = array_slice($words, 0, 2);

        // Include type if available
        foreach ($attributes as $attribute) {
            if (strpos($attribute, 'Type:') === 0) {
                $type = trim(str_replace('Type:', '', $attribute));
                return ucwords($type . ' ' . implode(' ', $significant));
            }
        }

        return ucwords(implode(' ', $significant));
    }

    /**
     * Generate a detailed category description
     */
    public function generateCategoryDescription($title, $searchContext, $attributes)
    {
        $description = "Category for ";

        if (!empty($attributes)) {
            $description .= implode(', ', array_map(function($attr) {
                return strtolower(str_replace(['Type:', 'Material:'], '', $attr));
            }, $attributes)) . " items. ";
        }

        $description .= "Suitable for items similar to: $title. ";
        $description .= "This category is designed to help organize and track items with similar characteristics and significance.";

        return $description;
    }

    /**
     * Get weight for different confidence factors
     */
    public function getFactorWeight($factor)
    {
        return [
            'direct_match' => 0.3,
            'context_overlap' => 0.2,
            'semantic_coherence' => 0.5
        ][$factor] ?? 0;
    }

    /**
     * Check for direct matches in category context
     */
    public function checkDirectMatch($title, $categoryContext)
    {
        $titleWords = explode(' ', strtolower($title));
        $contextWords = explode(' ', strtolower($categoryContext));

        $matches = array_intersect($titleWords, $contextWords);
        return count($matches) / count($titleWords);
    }

    /**
     * Analyze context overlap between search and category
     */
    public function analyzeContextOverlap($searchContext, $categoryContext)
    {
        $searchWords = str_word_count(strtolower($searchContext), 1);
        $categoryWords = str_word_count(strtolower($categoryContext), 1);

        $overlap = array_intersect($searchWords, $categoryWords);
        return count($overlap) / max(count($searchWords), count($categoryWords));
    }

    /**
     * Analyze semantic coherence between title and category
     */
    public function analyzeSemanticCoherence($title, $categoryContext)
    {
        // Use embeddings to check semantic similarity
        $titleEmbedding = $this->getTextEmbeddings($title);
        $contextEmbedding = $this->getTextEmbeddings($categoryContext);

        if ($titleEmbedding && $contextEmbedding) {
            return $this->cosineSimilarity($titleEmbedding, $contextEmbedding);
        }

        return 0.5; // Default fallback value
    }

    /**
     * Fallback category suggestion method
     */
    public function getFallbackCategorySuggestions($title, $categories)
    {
        // Simple text matching as fallback
        $matches = $categories->filter(function ($category) use ($title) {
            $titleLower = strtolower($title);
            $categoryLower = strtolower($category->name);

            // Direct substring match
            if (stripos($categoryLower, $titleLower) !== false ||
                stripos($titleLower, $categoryLower) !== false) {
                return true;
            }

            // Word match
            $titleWords = explode(' ', $titleLower);
            $categoryWords = explode(' ', $categoryLower);
            return !empty(array_intersect($titleWords, $categoryWords));
        });

        if ($matches->isEmpty()) {
            // If no direct matches, try fuzzy matching
            $matches = $categories->filter(function ($category) use ($title) {
                return similar_text(
                    strtolower($title),
                    strtolower($category->name)
                ) / strlen($title) > 0.4;
            });
        }

        return $matches->take(5)->pluck('id')->toArray();
    }

    /**
     * Suggest a new category based on item analysis with context
     */
    public function suggestNewCategoryWithContext($title, $searchContext, $categoryHierarchy)
    {
        // Extract key characteristics
        $attributes = $this->extractItemAttributes($title);

        // Generate category suggestion
        $suggestion = [
            'name' => $this->generateCategoryName($title, $attributes),
            'description' => $this->generateCategoryDescription($title, $searchContext, $attributes),
            'confidence' => 'AI_GENERATED'
        ];

        // Add semantic understanding
        $suggestion['semantic_understanding'] = $this->expandCategoryContext($title);

        // Add hierarchical information
        $suggestion['hierarchical_group'] = $categoryHierarchy[$this->getCategoryId($title, $attributes)] ?? 'Uncategorized';

        return $suggestion;
    }

    /**
     * Get category ID for semantic understanding
     */
    protected function getCategoryId($title, $attributes)
    {
        // Implement logic to determine the category ID based on the title and attributes
        // This is a placeholder and should be replaced with actual implementation
        return md5($title . implode(',', $attributes));
    }
}
