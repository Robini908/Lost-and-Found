<?php

namespace App\Livewire\Charts;

use Livewire\Component;
use App\Models\LostItem;
use App\Models\ItemMatch;
use Asantibanez\LivewireCharts\Models\PieChartModel;

class ItemsDistributionChart extends Component
{
    public function render()
    {
        // Get counts by item type
        $reportedCount = LostItem::where('item_type', 'reported')->count();
        $searchedCount = LostItem::where('item_type', 'searched')->count();
        $foundCount = LostItem::where('item_type', 'found')->count();
        $totalItems = $reportedCount + $searchedCount + $foundCount;

        // Get successful matches (similarity score >= 0.7)
        $matchedItems = ItemMatch::where('similarity_score', '>=', 0.7)->count();

        $pieChartModel = (new PieChartModel())
            ->setTitle('Item Status Distribution')
            ->addSlice('Reported', $reportedCount, '#EF4444')
            ->addSlice('Searched', $searchedCount, '#3B82F6')
            ->addSlice('Found', $foundCount, '#10B981')
            ->setAnimated(true)
            ->setDataLabelsEnabled(true);

        return view('livewire.charts.items-distribution-chart', [
            'pieChartModel' => $pieChartModel,
            'totalItems' => $totalItems,
            'reportedCount' => $reportedCount,
            'searchedCount' => $searchedCount,
            'foundCount' => $foundCount,
            'matchedItems' => $matchedItems
        ]);
    }

    public function onSliceClick($slice)
    {
        // Handle slice click event if needed
        $this->emit('itemTypeSelected', $slice);
    }
}
