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

    private $itemMatchingService;

    public function __construct(ItemMatchingService $itemMatchingService)
    {
        parent::__construct();
        $this->itemMatchingService = $itemMatchingService;
    }

    public function handle()
    {
        $users = \App\Models\User::all(); // Get all users from User model

        $this->info('Starting similarity score computation...');
        $bar = $this->output->createProgressBar(count($users));

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
                    try {
                        $scores = $this->calculateAndCacheScores($reportedItem, $foundItem);

                        Cache::put(
                            "similarity_scores_{$reportedItem->id}_{$foundItem->id}",
                            $scores,
                            now()->addHours(24)
                        );
                    } catch (\Exception $e) {
                        $this->error("Error processing items {$reportedItem->id} and {$foundItem->id}: {$e->getMessage()}");
                        continue;
                    }
                }
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Similarity scores precomputed and cached successfully.');
    }

    private function calculateAndCacheScores($reportedItem, $foundItem): array
    {
        $imageSimilarityScore = $this->itemMatchingService->calculateImageSimilarity(
            $reportedItem->images,
            $foundItem->images
        );

        $textSimilarityScore = $this->itemMatchingService->calculateTextSimilarityWithContext(
            $reportedItem->title . ' ' . $reportedItem->description,
            $foundItem->title . ' ' . $foundItem->description
        );

        $locationSimilarityScore = $this->itemMatchingService->calculateLocationSimilarity(
            [
                'lat' => $reportedItem->latitude,
                'lng' => $reportedItem->longitude
            ],
            [
                'lat' => $foundItem->latitude,
                'lng' => $foundItem->longitude
            ]
        );

        return [
            'image' => $imageSimilarityScore,
            'text' => $textSimilarityScore,
            'location' => $locationSimilarityScore,
        ];
    }
}
