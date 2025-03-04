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
        Log::info('LostItemObserver created event triggered', [
            'item_id' => $lostItem->id,
            'item_type' => $lostItem->item_type,
            'user_id' => $lostItem->user_id
        ]);

        // Process images if they exist
        if ($lostItem->images->isNotEmpty()) {
            foreach ($lostItem->images as $image) {
                try {
                    $this->imageProcessor->processImage($image->image_path, [
                        'max_width' => config('image.dimensions.max_width'),
                        'max_height' => config('image.dimensions.max_height'),
                        'quality' => config('image.quality.jpeg'),
                    ]);

                    Log::info('Image processed successfully', [
                        'image_id' => $image->id,
                        'path' => $image->image_path
                    ]);
                } catch (\Exception $e) {
                    Log::error('Failed to process image: ' . $e->getMessage(), [
                        'image_id' => $image->id,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                }
            }
        }

        // Only award points for found items
        if ($lostItem->item_type !== 'found') {
            Log::info('Item type is not "found", no points awarded', [
                'item_id' => $lostItem->id,
                'item_type' => $lostItem->item_type
            ]);
            return;
        }

        $pointsPerFoundItem = 100; // Fixed at 100 points per found item

        try {
            DB::beginTransaction();

            // Add points to user
            $user = $lostItem->user;
            Log::info('Before adding points', [
                'user_id' => $user->id,
                'current_points' => $user->reward_points
            ]);

            $user->increment('reward_points', $pointsPerFoundItem);

            Log::info('After adding points', [
                'user_id' => $user->id,
                'new_points' => $user->reward_points
            ]);

            // Create reward history
            $history = RewardHistory::create([
                'user_id' => $user->id,
                'type' => 'earned',
                'points' => $pointsPerFoundItem,
                'description' => 'Reported found item: ' . $lostItem->title,
                'lost_item_id' => $lostItem->id,
            ]);

            Log::info('Reward history created', [
                'history_id' => $history->id,
                'user_id' => $user->id,
                'points' => $pointsPerFoundItem
            ]);

            DB::commit();

            Log::info('Points awarded successfully', [
                'user_id' => $user->id,
                'points' => $pointsPerFoundItem,
                'item_id' => $lostItem->id
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to award points for found item: ' . $e->getMessage(), [
                'user_id' => $lostItem->user_id,
                'item_id' => $lostItem->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
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
