<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\LostItem;
use App\Models\ItemMatch;
use Carbon\Carbon;

class DashboardStats extends Component
{
    public $totalLostItems;
    public $totalFoundItems;
    public $last30DaysLostItems;
    public $last30DaysFoundItems;
    public $successfulMatches;
    public $recoveryRate;

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

        // Calculate successful matches (where similarity score is >= 0.7)
        $this->successfulMatches = ItemMatch::where('similarity_score', '>=', 0.7)->count();

        // Calculate recovery rate
        $this->recoveryRate = $this->totalLostItems > 0
            ? ($this->successfulMatches / $this->totalLostItems) * 100
            : 0;
    }

    public function getListeners()
    {
        return [
            'refresh-stats' => 'loadStats',
            'echo:items,ItemMatched' => 'loadStats',
            'echo:items,ItemCreated' => 'loadStats',
            'echo:items,ItemUpdated' => 'loadStats',
            'echo:items,ItemDeleted' => 'loadStats'
        ];
    }

    public function render()
    {
        return view('livewire.dashboard-stats', [
            'totalLostItems' => $this->totalLostItems,
            'totalFoundItems' => $this->totalFoundItems,
            'last30DaysLostItems' => $this->last30DaysLostItems,
            'last30DaysFoundItems' => $this->last30DaysFoundItems,
            'successfulMatches' => $this->successfulMatches,
            'recoveryRate' => $this->recoveryRate
        ]);
    }
}
