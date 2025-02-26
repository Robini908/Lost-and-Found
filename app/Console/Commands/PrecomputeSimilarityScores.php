<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\LostItem;
use App\Services\ItemMatchingService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;

class PrecomputeSimilarityScores extends Command
{
    protected $signature = 'items:precompute-similarity-scores';
    protected $description = 'Precompute and cache similarity scores for items';

    public function __construct(private ItemMatchingService $itemMatchingService)
    {
        parent::__construct();
    }

    public function handle()
    {
        $users = Auth::all(); // Assuming you have a way to get all users

        foreach ($users as $user) {
            $reportedItems = LostItem::where('user_id', $user->id)
                ->whereIn('item_type', ['reported', 'searched'])
                ->with('images')
                ->get();

            $foundItems = LostItem::where('item_type', 'found')
                ->with('images')
                ->get();

            foreach ($reportedItems as $reportedItem) {
                foreach ($foundItems as $foundItem) {
                    $imageSimilarityScore = $this->itemMatchingService->calculateImageSimilarity(
                        $reportedItem->images,
                        $foundItem->images
                    );

                    $textSimilarityScore = $this->itemMatchingService->calculateTextSimilarity(
                        $reportedItem->title . ' ' . $reportedItem->description,
                        $foundItem->title . ' ' . $foundItem->description
                    );

                    $locationSimilarityScore = $this->itemMatchingService->calculateLocationSimilarity(
                        $reportedItem->geolocation,
                        $foundItem->geolocation
                    );

                    Cache::put("similarity_scores_{$reportedItem->id}_{$foundItem->id}", [
                        'image' => $imageSimilarityScore,
                        'text' => $textSimilarityScore,
                        'location' => $locationSimilarityScore,
                    ], now()->addMinutes(60)); // Cache for 1 hour
                }
            }
        }

        $this->info('Similarity scores precomputed and cached successfully.');
    }
}