<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\LostItem;
use App\Models\ItemMatch;
use Carbon\Carbon;

class LandingPageStats extends Component
{
    public $totalLostItems;
    public $totalFoundItems;
    public $last30DaysLostItems;
    public $last30DaysFoundItems;
    public $successfulMatches;
    public $recoveryRate;
    public $activeUsers;
    public $averageMatchTime;

    public function mount()
    {
        $this->loadStats();
    }

    public function loadStats()
    {
        // Calculate total items
        $this->totalLostItems = LostItem::where('item_type', LostItem::TYPE_REPORTED)->count();
        $this->totalFoundItems = LostItem::where('item_type', LostItem::TYPE_FOUND)->count();

        // Calculate last 30 days statistics
        $last30DaysStart = now()->subDays(30)->startOfDay();
        $this->last30DaysLostItems = LostItem::where('item_type', LostItem::TYPE_REPORTED)
            ->where('created_at', '>=', $last30DaysStart)
            ->count();
        $this->last30DaysFoundItems = LostItem::where('item_type', LostItem::TYPE_FOUND)
            ->where('created_at', '>=', $last30DaysStart)
            ->count();

        // Get successful matches
        $successfulMatches = ItemMatch::with(['lostItem', 'foundItem'])
            ->where('similarity_score', '>=', 0.7)
            ->get();

        $this->successfulMatches = $successfulMatches->count();

        // Calculate recovery rate
        $this->recoveryRate = $this->totalLostItems > 0
            ? ($this->successfulMatches / $this->totalLostItems) * 100
            : 0;

        // Calculate average match time
        $totalMatchTime = 0;
        $validMatches = 0;

        foreach ($successfulMatches as $match) {
            if ($match->lostItem && $match->foundItem) {
                $matchTime = $this->calculateMatchTime($match);
                if ($matchTime > 0) {
                    $totalMatchTime += $matchTime;
                    $validMatches++;
                }
            }
        }

        if ($validMatches > 0) {
            $avgMilliseconds = $totalMatchTime / $validMatches;

            // Format the time appropriately
            if ($avgMilliseconds < 100) {
                $this->averageMatchTime = 'Lightning Fast';
            } elseif ($avgMilliseconds < 1000) {
                $this->averageMatchTime = round($avgMilliseconds) . ' milliseconds';
            } elseif ($avgMilliseconds < 10000) {
                $this->averageMatchTime = number_format($avgMilliseconds / 1000, 1) . ' seconds';
            } elseif ($avgMilliseconds < 60000) {
                $this->averageMatchTime = round($avgMilliseconds / 1000) . ' seconds';
            } elseif ($avgMilliseconds < 3600000) {
                $minutes = round($avgMilliseconds / 60000);
                $this->averageMatchTime = $minutes . ' ' . ($minutes === 1 ? 'minute' : 'minutes');
            } else {
                $hours = round($avgMilliseconds / 3600000, 1);
                $this->averageMatchTime = $hours . ' ' . ($hours === 1 ? 'hour' : 'hours');
            }
        } else {
            $this->averageMatchTime = 'N/A';
        }

        // Calculate active users (users with activity in last 30 days)
        $this->activeUsers = LostItem::where('created_at', '>=', $last30DaysStart)
            ->distinct('user_id')
            ->count('user_id');
    }

    /**
     * Calculate the match time based on ItemMatchingService logic
     * This simulates the actual matching process time
     */
    protected function calculateMatchTime($match)
    {
        if (!$match->lostItem || !$match->foundItem) {
            return 0;
        }

        // Base processing time (initialization and setup)
        $baseTime = 150; // ms

        // Text Similarity Processing
        $textTime = $this->calculateTextSimilarityTime($match->lostItem, $match->foundItem);

        // Image Processing (if images exist)
        $imageTime = $this->calculateImageProcessingTime($match->lostItem, $match->foundItem);

        // Category Matching
        $categoryTime = $this->calculateCategoryMatchingTime($match->lostItem, $match->foundItem);

        // Location Processing
        $locationTime = $this->calculateLocationProcessingTime($match->lostItem, $match->foundItem);

        // Date Similarity
        $dateTime = $this->calculateDateSimilarityTime($match->lostItem, $match->foundItem);

        return $baseTime + $textTime + $imageTime + $categoryTime + $locationTime + $dateTime;
    }

    protected function calculateTextSimilarityTime($lostItem, $foundItem)
    {
        // Base time for text processing
        $baseTextTime = 100;

        // Calculate complexity based on text length
        $lostText = $lostItem->title . ' ' . ($lostItem->description ?? '');
        $foundText = $foundItem->title . ' ' . ($foundItem->description ?? '');

        // Add time based on text length (1ms per 50 characters)
        $lengthFactor = (strlen($lostText) + strlen($foundText)) / 50;

        // Add time for embedding generation (200ms base + length factor)
        $embeddingTime = 200 + ($lengthFactor * 0.5);

        return $baseTextTime + ceil($embeddingTime);
    }

    protected function calculateImageProcessingTime($lostItem, $foundItem)
    {
        $baseImageTime = 300; // Base time for image processing setup

        // Count images
        $lostImages = $lostItem->images()->count();
        $foundImages = $foundItem->images()->count();

        if ($lostImages === 0 || $foundImages === 0) {
            return 0;
        }

        // Add processing time for each image (250ms per image for embedding generation)
        $imageProcessingTime = ($lostImages + $foundImages) * 250;

        // Add time for similarity comparison
        $comparisonTime = $lostImages * $foundImages * 50; // 50ms per comparison

        return $baseImageTime + $imageProcessingTime + $comparisonTime;
    }

    protected function calculateCategoryMatchingTime($lostItem, $foundItem)
    {
        // Category matching is a simple lookup and comparison
        return 10; // 10ms for database query and comparison
    }

    protected function calculateLocationProcessingTime($lostItem, $foundItem)
    {
        // Base time for location processing
        $baseLocationTime = 20;

        // If both items have coordinates, add time for haversine calculation
        if ($lostItem->latitude && $lostItem->longitude &&
            $foundItem->latitude && $foundItem->longitude) {
            return $baseLocationTime + 30; // Additional 30ms for coordinate calculations
        }

        return $baseLocationTime;
    }

    protected function calculateDateSimilarityTime($lostItem, $foundItem)
    {
        // Simple timestamp comparison
        return 5; // 5ms for date comparison
    }

    public function getListeners()
    {
        return [
            'echo:items,ItemMatched' => 'loadStats',
            'echo:items,ItemCreated' => 'loadStats',
            'echo:items,ItemUpdated' => 'loadStats',
            'echo:items,ItemDeleted' => 'loadStats'
        ];
    }

    public function render()
    {
        return view('livewire.landing-page-stats');
    }
}
