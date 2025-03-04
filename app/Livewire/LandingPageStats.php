<?php

namespace App\Livewire;

use Carbon\Carbon;
use App\Models\User;
use Livewire\Component;
use App\Models\LostItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class LandingPageStats extends Component
{
    public $stats = [
        'matchRate' => 0,
        'avgRecoveryTime' => 0,
        'totalItems' => 0,
        'recentActivity' => 0
    ];

    public function mount()
    {
        $this->loadStats();
    }

    public function loadStats()
    {
        // Cache stats for 5 minutes to improve performance
        $this->stats = Cache::remember('landing_page_stats', 300, function () {
            $totalReportedItems = LostItem::whereIn('item_type', ['reported', 'searched'])->count();
            $totalMatchedItems = LostItem::whereIn('item_type', ['reported', 'searched'])
                ->whereNotNull('matched_found_item_id')
                ->count();

            // Get items from last 7 days
            $recentItems = LostItem::where('created_at', '>=', now()->subDays(7))->count();

            return [
                'matchRate' => $totalReportedItems > 0
                    ? round(($totalMatchedItems / $totalReportedItems) * 100)
                    : 0,
                'avgRecoveryTime' => $this->calculateAverageRecoveryTime(),
                'totalItems' => $this->getItemsBreakdown(),
                'recentActivity' => $recentItems
            ];
        });
    }

    private function calculateAverageRecoveryTime()
    {
        $avgHours = LostItem::whereIn('item_type', ['reported', 'searched'])
            ->whereNotNull('matched_found_item_id')
            ->whereNotNull('updated_at')
            ->avg(DB::raw('TIMESTAMPDIFF(HOUR, created_at, updated_at)'));

        return round($avgHours) ?: 24;
    }

    private function getItemsBreakdown()
    {
        return [
            'reported' => LostItem::where('item_type', 'reported')->count(),
            'found' => LostItem::where('item_type', 'found')->count(),
            'searched' => LostItem::where('item_type', 'searched')->count(),
            'matched' => LostItem::whereNotNull('matched_found_item_id')->count(),
            'verified' => LostItem::where('is_verified', true)->count()
        ];
    }

    public function render()
    {
        return view('livewire.landing-page-stats');
    }
}
