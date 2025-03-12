<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\LostItem;
use App\Models\ItemMatch;
use App\Services\ItemMatchingService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;

class ProcessItemMatching implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 300; // 5 minutes
    public $tries = 3;
    public $backoff = [30, 60, 120]; // Retry after 30s, 1m, 2m

    protected $itemId;

    public function __construct($itemId)
    {
        $this->itemId = $itemId;
    }

    public function handle(ItemMatchingService $matchingService)
    {
        try {
            $item = LostItem::with(['images', 'category'])->findOrFail($this->itemId);

            // Check if already processed recently
            $cacheKey = "processing_item:{$this->itemId}";
            if (Cache::has($cacheKey)) {
                return;
            }

            // Set processing lock
            Cache::put($cacheKey, true, now()->addMinutes(5));

            $matches = $matchingService->findMatches($item);

            if ($matches->isEmpty()) {
                return;
            }

            DB::beginTransaction();
            try {
                $matchCount = 0;
                foreach ($matches as $match) {
                    ItemMatch::updateOrCreate(
                        [
                            'lost_item_id' => $item->id,
                            'found_item_id' => $match['found_item']->id
                        ],
                        [
                            'similarity_score' => $match['similarity'],
                            'matched_at' => now()
                        ]
                    );
                    $matchCount++;
                }
                DB::commit();

                // Broadcast match completion
                event('matchProcessed', [
                    'item_id' => $item->id,
                    'matches_found' => $matchCount
                ]);

                // Clear item cache
                Cache::forget("item_matches:{$item->id}");

            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Error processing matches in job', [
                    'item_id' => $item->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                throw $e;
            }

        } catch (\Exception $e) {
            Log::error('Error in ProcessItemMatching job', [
                'item_id' => $this->itemId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        } finally {
            Cache::forget("processing_item:{$this->itemId}");
        }
    }

    public function failed(\Throwable $exception)
    {
        Log::error('ProcessItemMatching job failed', [
            'item_id' => $this->itemId,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString()
        ]);
    }
}
