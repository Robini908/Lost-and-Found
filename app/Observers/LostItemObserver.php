<?php

namespace App\Observers;

use App\Models\LostItem;
use App\Models\Setting;
use App\Models\RewardHistory;
use App\Services\ImageProcessingService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LostItemObserver
{
    protected $imageProcessor;

    public function __construct(ImageProcessingService $imageProcessor)
    {
        $this->imageProcessor = $imageProcessor;
    }

    /**
     * Handle the LostItem "created" event.
     */
    public function created(LostItem $lostItem): void
    {
        // Process images if they exist
        if ($lostItem->images->isNotEmpty()) {
            foreach ($lostItem->images as $image) {
                try {
                    $this->imageProcessor->processImage($image->image_path, [
                        'max_width' => config('image.dimensions.max_width', 1200),
                        'max_height' => config('image.dimensions.max_height', 1200),
                        'quality' => config('image.quality.jpeg', 85),
                    ]);
                } catch (\Exception $e) {
                    Log::error('Failed to process image: ' . $e->getMessage(), [
                        'image_id' => $image->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }
        }

        // Only award points for found items
        if ($lostItem->item_type !== 'found') {
            return;
        }

        $pointsPerFoundItem = 100; // Fixed at 100 points per found item

        try {
            DB::beginTransaction();

            // Add points to user
            $user = $lostItem->user;
            $user->increment('reward_points', $pointsPerFoundItem);

            // Create reward history
            RewardHistory::create([
                'user_id' => $user->id,
                'type' => 'earned',
                'points' => $pointsPerFoundItem,
                'description' => 'Reported found item: ' . $lostItem->title,
                'lost_item_id' => $lostItem->id,
            ]);

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to award points for found item: ' . $e->getMessage());
        }
    }

    /**
     * Handle the LostItem "updated" event.
     */
    public function updated(LostItem $lostItem): void
    {
        //
    }

    /**
     * Handle the LostItem "deleted" event.
     */
    public function deleted(LostItem $lostItem): void
    {
        //
    }

    /**
     * Handle the LostItem "restored" event.
     */
    public function restored(LostItem $lostItem): void
    {
        //
    }

    /**
     * Handle the LostItem "force deleted" event.
     */
    public function forceDeleted(LostItem $lostItem): void
    {
        //
    }
}
