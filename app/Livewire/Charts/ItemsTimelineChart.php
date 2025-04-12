<?php

namespace App\Livewire\Charts;

use Livewire\Component;
use App\Models\LostItem;
use Carbon\Carbon;
use Asantibanez\LivewireCharts\Models\LineChartModel;
use Illuminate\Support\Facades\DB;

class ItemsTimelineChart extends Component
{
    public $timeRange = 'last_30_days';

    protected $listeners = ['updateTimeRange'];

    public function updateTimeRange($range)
    {
        $this->timeRange = $range;
    }

    public function render()
    {
        $startDate = $this->getStartDate();
        $endDate = now();

        // Get data for all types
        $data = LostItem::select('item_type', DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('date', 'item_type')
            ->orderBy('date')
            ->get();

        // Create line chart model
        $lineChartModel = (new LineChartModel())
            ->setTitle('Items by Status Over Time')
            ->setAnimated(true)
            ->setDataLabelsEnabled(false);

        // Colors for each type
        $colors = [
            'reported' => '#EF4444',
            'searched' => '#3B82F6',
            'found' => '#10B981'
        ];

        // Process data for each date
        $dates = $data->pluck('date')->unique()->values();
        foreach ($dates as $date) {
            $formattedDate = Carbon::parse($date)->format('M d');

            // Add point for each type
            foreach ($colors as $type => $color) {
                $count = $data->where('date', $date)
                            ->where('item_type', $type)
                            ->first()?->count ?? 0;

                $lineChartModel->addPoint(
                    ucfirst($type),
                    $count,
                    $color,
                    ['date' => $date]
                );
            }
        }

        return view('livewire.charts.items-timeline-chart', [
            'lineChartModel' => $lineChartModel
        ]);
    }

    protected function getStartDate()
    {
        return match($this->timeRange) {
            'last_7_days' => now()->subDays(7)->startOfDay(),
            'last_30_days' => now()->subDays(30)->startOfDay(),
            'last_90_days' => now()->subDays(90)->startOfDay(),
            'last_year' => now()->subYear()->startOfDay(),
            default => now()->subDays(30)->startOfDay(),
        };
    }
}
