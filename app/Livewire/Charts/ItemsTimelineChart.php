<?php

namespace App\Livewire\Charts;

use Livewire\Component;
use App\Models\LostItem;
use Carbon\Carbon;
use Asantibanez\LivewireCharts\Models\LineChartModel;
use Illuminate\Support\Facades\DB;

class ItemsTimelineChart extends Component
{
    public $days = 30;
    public $chartType = 'status'; // status, verification, claims

    protected $queryString = ['days', 'chartType'];

    public function render()
    {
        $startDate = Carbon::now()->subDays($this->days);
        $lineChartModel = (new LineChartModel())->setAnimated(true);

        switch ($this->chartType) {
            case 'status':
                $items = LostItem::where('created_at', '>=', $startDate)
                    ->select('item_type', DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
                    ->groupBy('date', 'item_type')
                    ->orderBy('date')
                    ->get();

                $lineChartModel->setTitle('Items by Status Over Time');

                $dates = $items->pluck('date')->unique()->values();
                foreach ($dates as $date) {
                    $reportedCount = $items->where('date', $date)->where('item_type', LostItem::TYPE_REPORTED)->first()?->count ?? 0;
                    $searchedCount = $items->where('date', $date)->where('item_type', LostItem::TYPE_SEARCHED)->first()?->count ?? 0;
                    $foundCount = $items->where('date', $date)->where('item_type', LostItem::TYPE_FOUND)->first()?->count ?? 0;

                    $formattedDate = Carbon::parse($date)->format('M d');
                    $lineChartModel->addPoint($formattedDate, $reportedCount, '#EF4444', ['date' => $date, 'type' => 'reported'])
                        ->addPoint($formattedDate, $searchedCount, '#3B82F6', ['date' => $date, 'type' => 'searched'])
                        ->addPoint($formattedDate, $foundCount, '#10B981', ['date' => $date, 'type' => 'found']);
                }
                break;

            case 'verification':
                $items = LostItem::where('created_at', '>=', $startDate)
                    ->select(
                        DB::raw('DATE(created_at) as date'),
                        DB::raw('SUM(CASE WHEN is_verified = 1 THEN 1 ELSE 0 END) as verified_count'),
                        DB::raw('COUNT(*) - SUM(CASE WHEN is_verified = 1 THEN 1 ELSE 0 END) as unverified_count')
                    )
                    ->groupBy('date')
                    ->orderBy('date')
                    ->get();

                $lineChartModel->setTitle('Item Verification Status Over Time');

                foreach ($items as $item) {
                    $formattedDate = Carbon::parse($item->date)->format('M d');
                    $lineChartModel->addPoint($formattedDate, $item->verified_count, '#10B981', ['date' => $item->date, 'type' => 'verified'])
                        ->addPoint($formattedDate, $item->unverified_count, '#EF4444', ['date' => $item->date, 'type' => 'unverified']);
                }
                break;

            case 'claims':
                $items = LostItem::where('created_at', '>=', $startDate)
                    ->select(
                        DB::raw('DATE(created_at) as date'),
                        DB::raw('SUM(CASE WHEN claimed_by IS NOT NULL THEN 1 ELSE 0 END) as claimed_count'),
                        DB::raw('SUM(CASE WHEN matched_found_item_id IS NOT NULL THEN 1 ELSE 0 END) as matched_count')
                    )
                    ->groupBy('date')
                    ->orderBy('date')
                    ->get();

                $lineChartModel->setTitle('Claims and Matches Over Time');

                foreach ($items as $item) {
                    $formattedDate = Carbon::parse($item->date)->format('M d');
                    $lineChartModel->addPoint($formattedDate, $item->claimed_count, '#8B5CF6', ['date' => $item->date, 'type' => 'claimed'])
                        ->addPoint($formattedDate, $item->matched_count, '#F59E0B', ['date' => $item->date, 'type' => 'matched']);
                }
                break;
        }

        return view('livewire.charts.items-timeline-chart', [
            'lineChartModel' => $lineChartModel
        ]);
    }

    public function onPointClick($point)
    {
        $this->emit('dateSelected', [
            'date' => $point['extras']['date'],
            'type' => $point['extras']['type']
        ]);
    }
}
