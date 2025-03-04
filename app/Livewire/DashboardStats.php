<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\LostItem;
use Carbon\Carbon;

class DashboardStats extends Component
{
    public $stats = [];

    public function mount()
    {
        $this->loadStats();
    }

    public function loadStats()
    {
        $lastMonth = Carbon::now()->subDays(30);

        $totalLostItems = LostItem::whereIn('item_type', [LostItem::TYPE_REPORTED, LostItem::TYPE_SEARCHED])->count();
        $totalFoundItems = LostItem::where('item_type', LostItem::TYPE_FOUND)->count();
        $lastMonthLostItems = LostItem::whereIn('item_type', [LostItem::TYPE_REPORTED, LostItem::TYPE_SEARCHED])
            ->where('created_at', '>=', $lastMonth)
            ->count();
        $lastMonthFoundItems = LostItem::where('item_type', LostItem::TYPE_FOUND)
            ->where('created_at', '>=', $lastMonth)
            ->count();

        // Calculate successful matches
        $successfulMatches = LostItem::whereNotNull('matched_found_item_id')->count();
        $totalItems = $totalLostItems + $totalFoundItems;
        $matchRate = $totalItems > 0 ? round(($successfulMatches / $totalItems) * 100, 1) : 0;

        // Calculate recovery trend
        $previousMonthMatches = LostItem::whereNotNull('matched_found_item_id')
            ->where('created_at', '>=', Carbon::now()->subMonths(2))
            ->where('created_at', '<', Carbon::now()->subMonth())
            ->count();

        $currentMonthMatches = LostItem::whereNotNull('matched_found_item_id')
            ->where('created_at', '>=', Carbon::now()->subMonth())
            ->count();

        $trend = 'Stable';
        if ($previousMonthMatches > 0) {
            $changePercent = (($currentMonthMatches - $previousMonthMatches) / $previousMonthMatches) * 100;
            if ($changePercent > 5) {
                $trend = 'Increasing';
            } elseif ($changePercent < -5) {
                $trend = 'Decreasing';
            }
        }

        $this->stats = [
            'totalLostItems' => $totalLostItems,
            'totalFoundItems' => $totalFoundItems,
            'lastMonthLostItems' => $lastMonthLostItems,
            'lastMonthFoundItems' => $lastMonthFoundItems,
            'successfulMatches' => $successfulMatches,
            'matchRate' => $matchRate . '%',
            'recoveryRate' => round(($successfulMatches / ($totalLostItems ?: 1)) * 100, 1) . '%',
            'recoveryTrend' => $trend
        ];
    }

    public function getListeners()
    {
        return [
            'refresh-stats' => 'loadStats',
            'echo:items,ItemMatched' => 'loadStats',
            'echo:items,ItemCreated' => 'loadStats'
        ];
    }

    public function render()
    {
        return view('livewire.dashboard-stats');
    }
}
