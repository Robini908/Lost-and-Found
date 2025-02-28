<?php

namespace App\Livewire\Charts;

use Livewire\Component;
use App\Models\LostItem;
use Asantibanez\LivewireCharts\Models\PieChartModel;
use Illuminate\Support\Facades\DB;

class ItemsDistributionChart extends Component
{
    public function render()
    {
        $itemCounts = LostItem::select('item_type', DB::raw('count(*) as count'))
            ->groupBy('item_type')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->item_type => $item->count];
            });

        $pieChartModel = (new PieChartModel())
            ->setTitle('Item Status Distribution')
            ->addSlice('Reported', $itemCounts['reported'] ?? 0, '#EF4444')
            ->addSlice('Searched', $itemCounts['searched'] ?? 0, '#3B82F6')
            ->addSlice('Found', $itemCounts['found'] ?? 0, '#10B981')
            ->setAnimated(true)
            ->setType('donut')
            ->withOnSliceClickEvent('onSliceClick')
            ->setDataLabelsEnabled(true);

        // Get additional statistics
        $totalItems = $itemCounts->sum();
        $claimedItems = LostItem::whereNotNull('claimed_by')->count();
        $verifiedItems = LostItem::where('is_verified', true)->count();
        $matchedItems = LostItem::whereNotNull('matched_found_item_id')->count();

        return view('livewire.charts.items-distribution-chart', [
            'pieChartModel' => $pieChartModel,
            'stats' => [
                'total' => $totalItems,
                'claimed' => $claimedItems,
                'verified' => $verifiedItems,
                'matched' => $matchedItems,
            ]
        ]);
    }

    public function onSliceClick($slice)
    {
        $this->emit('itemTypeSelected', $slice['label']);
    }
}
