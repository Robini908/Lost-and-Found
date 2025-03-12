<?php

namespace App\Services;

use GuzzleHttp\Client;
use App\Models\LostItem;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Jobs\ProcessImageEmbeddings;

class ItemMatchingService
{
    protected $client;
    protected $apiToken;

    public function __construct()
    {
        $this->client = new Client();
        $this->apiToken = config('services.huggingface.token');

        if (empty($this->apiToken)) {
            throw new \RuntimeException('HuggingFace API token not configured. Please check your HUGGINGFACE_API_TOKEN in .env');
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

        // Process image embeddings
        $job = new ProcessImageEmbeddings($imageUrl, $imageId);
        return $job->handle(); // For now, process synchronously. Later we can switch to ->dispatch()
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

                $processingTime = microtime(true) - $itemStartTime;
                Log::debug('Item matching result', [
                    'lost_item_id' => $lostItem->id,
                    'found_item_id' => $foundItem->id,
                    'similarity_score' => $similarity,
                    'processing_time_ms' => round($processingTime * 1000, 2),
                    'matched' => $similarity > 0.7
                ]);

                if ($similarity > 0.7) {
                    $matches->push([
                        'found_item' => $foundItem,
                        'similarity' => $similarity,
                        'processing_time_ms' => round($processingTime * 1000, 2)
                    ]);
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

                    if ($similarity > 0.7) {
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
     * Get category suggestions based on item title using AI
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
            return $this->getAIBasedCategorySuggestions($title, $categories);
        } catch (\Exception $e) {
            Log::error('AI-based category matching failed, falling back to simple matching', [
                'error' => $e->getMessage(),
                'title' => $title
            ]);
            return $this->getFallbackCategorySuggestions($title, $categories);
        }
    }

    /**
     * Get category suggestions using AI embeddings
     *
     * @param string $title
     * @param \Illuminate\Database\Eloquent\Collection $categories
     * @return array
     */
    protected function getAIBasedCategorySuggestions($title, $categories)
    {
        // Prepare context-rich texts for embeddings
        $textsToProcess = [
            'search' => $this->enrichTitleContext($title)
        ];

        // Add category contexts
        foreach ($categories as $category) {
            $textsToProcess['category_' . $category->id] = $this->buildCategoryContext($category);
        }

        // Get embeddings for all texts
        $embeddings = $this->getBatchTextEmbeddings($textsToProcess);
        if (!isset($embeddings['search'])) {
            throw new \Exception('Failed to generate embeddings for search text');
        }

        $titleEmbedding = $embeddings['search'];
        $similarities = [];

        // Calculate similarities with dynamic threshold
        $baseThreshold = 0.3;
        $titleLength = strlen($title);
        $dynamicThreshold = $this->calculateDynamicThreshold($titleLength);

        foreach ($categories as $category) {
            $categoryEmbedding = $embeddings['category_' . $category->id] ?? null;
            if ($categoryEmbedding) {
                $similarity = $this->cosineSimilarity($titleEmbedding, $categoryEmbedding);

                // Apply semantic boosting based on keyword matches
                $similarity = $this->applySemanticBoosting($similarity, $title, $category);

                if ($similarity >= $dynamicThreshold) {
                    $similarities[] = [
                        'id' => $category->id,
                        'similarity' => $similarity
                    ];
                }
            }
        }

        // Sort and return top matches
        return collect($similarities)
            ->sortByDesc('similarity')
            ->take(5)
            ->pluck('id')
            ->toArray();
    }

    /**
     * Enrich title context for better semantic matching
     *
     * @param string $title
     * @return string
     */
    protected function enrichTitleContext($title)
    {
        // Clean and normalize the title
        $cleanTitle = trim(preg_replace('/\s+/', ' ', $title));

        // Extract key terms and add context
        $terms = explode(' ', strtolower($cleanTitle));
        $context = "Item title: $cleanTitle. ";

        // Add type context if detectable
        $typeIndicators = [
            'phone' => 'electronic device',
            'wallet' => 'personal accessory',
            'card' => 'document or payment method',
            'key' => 'access tool',
            'bag' => 'container or accessory',
            'book' => 'reading material',
            'document' => 'official paper'
        ];

        foreach ($terms as $term) {
            if (isset($typeIndicators[$term])) {
                $context .= "This appears to be a {$typeIndicators[$term]}. ";
                break;
            }
        }

        return $context;
    }

    /**
     * Build rich context for category matching
     *
     * @param \App\Models\Category $category
     * @return string
     */
    protected function buildCategoryContext($category)
    {
        $context = "Category: {$category->name}. ";
        if ($category->description) {
            $context .= "Description: {$category->description}. ";
        }

        // Add semantic context based on category name
        $context .= "This category is suitable for ";
        $context .= strtolower($category->name);
        $context .= " type items and similar objects.";

        return $context;
    }

    /**
     * Calculate dynamic similarity threshold based on input
     *
     * @param int $titleLength
     * @return float
     */
    protected function calculateDynamicThreshold($titleLength)
    {
        // Shorter titles need higher threshold to prevent false positives
        if ($titleLength <= 5) {
            return 0.4;
        } elseif ($titleLength <= 10) {
            return 0.35;
        }
        return 0.3;
    }

    /**
     * Apply semantic boosting to similarity score
     *
     * @param float $similarity
     * @param string $title
     * @param \App\Models\Category $category
     * @return float
     */
    protected function applySemanticBoosting($similarity, $title, $category)
    {
        $boost = 0;

        // Direct name match boost
        if (stripos($category->name, $title) !== false || stripos($title, $category->name) !== false) {
            $boost += 0.1;
        }

        // Description match boost
        if ($category->description && stripos($category->description, $title) !== false) {
            $boost += 0.05;
        }

        // Prevent similarity from exceeding 1.0
        return min(1.0, $similarity + $boost);
    }

    /**
     * Fallback category suggestion method
     *
     * @param string $title
     * @param \Illuminate\Database\Eloquent\Collection $categories
     * @return array
     */
    protected function getFallbackCategorySuggestions($title, $categories)
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
}
