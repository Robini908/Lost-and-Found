<?php

namespace App\Livewire\Charts;

use Livewire\Component;
use App\Models\LostItem;
use App\Models\Category;
use App\Models\ItemMatch;
use Asantibanez\LivewireCharts\Models\ColumnChartModel;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CategoryDistributionChart extends Component
{
    public $sortBy = 'total';
    public $timeRange = 'all';
    public $limit = 10;
    public $selectedCategory = null;
    public $showTrends = true;

    protected $queryString = ['sortBy', 'timeRange', 'limit'];

    public function mount()
    {
        $this->sortBy = request()->get('sortBy', 'total');
        $this->timeRange = request()->get('timeRange', 'all');
    }

    public function render()
    {
        // Define startDate based on timeRange before using it
        $startDate = match($this->timeRange) {
            'month' => Carbon::now()->startOfMonth(),
            'week' => Carbon::now()->startOfWeek(),
            '30days' => Carbon::now()->subDays(30),
            'all' => null,
            default => null,
        };

        // Define the recent items threshold date (7 days ago)
        $recentThreshold = now()->subDays(7);

        // Get categories with detailed statistics
        $categories = Category::select(
            'categories.*',
            DB::raw('COUNT(DISTINCT lost_items.id) as total_items'),
            DB::raw('COUNT(DISTINCT CASE WHEN lost_items.item_type = "reported" THEN lost_items.id END) as lost_count'),
            DB::raw('COUNT(DISTINCT CASE WHEN lost_items.item_type = "found" THEN lost_items.id END) as found_count'),
            DB::raw('COUNT(DISTINCT CASE WHEN lost_items.item_type = "searched" THEN lost_items.id END) as searched_count'),
            DB::raw('COUNT(DISTINCT CASE WHEN item_matches.id IS NOT NULL AND item_matches.similarity_score >= 0.7 THEN item_matches.id END) as matched_count'),
            DB::raw('AVG(CASE WHEN item_matches.id IS NOT NULL THEN item_matches.similarity_score ELSE NULL END) as avg_match_score'),
            DB::raw("COUNT(DISTINCT CASE WHEN lost_items.created_at >= '{$recentThreshold}' THEN lost_items.id END) as recent_items")
        )
        ->leftJoin('lost_items', 'categories.id', '=', 'lost_items.category_id')
        ->leftJoin('item_matches', function($join) {
            $join->on('lost_items.id', '=', 'item_matches.lost_item_id')
                ->orOn('lost_items.id', '=', 'item_matches.found_item_id');
        })
        ->when($startDate !== null, function($query) use ($startDate) {
            $query->where('lost_items.created_at', '>=', $startDate);
        })
        ->groupBy('categories.id')
        ->orderBy($this->getSortColumn(), 'desc')
        ->get();

        // Calculate success metrics
        $categories = $categories->map(function ($category) {
            $totalItems = $category->total_items ?: 1; // Prevent division by zero

            return array_merge($category->toArray(), [
                'match_rate' => ($category->matched_count / $totalItems) * 100,
                'found_rate' => ($category->found_count / $totalItems) * 100,
                'weekly_trend' => ($category->recent_items / $totalItems) * 100,
                'avg_match_score' => $category->avg_match_score * 100,
                'trend_indicator' => $this->getTrendIndicator($category->recent_items, $totalItems),
                'success_score' => $this->calculateSuccessScore($category)
            ]);
        });

        // Create chart model
        $columnChartModel = (new ColumnChartModel())
            ->setTitle('Category Distribution')
            ->setAnimated(true)
            ->setDataLabelsEnabled(true)
            ->withOnColumnClickEventName('onColumnClick');

        // Color palette
        $colors = [
            '#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6',
            '#EC4899', '#6366F1', '#14B8A6', '#F97316', '#06B6D4'
        ];

        // Add data to chart based on sort type
        foreach ($categories->take($this->limit) as $index => $category) {
            $value = $this->getChartValue($category);
            $columnChartModel->addColumn(
                $category['name'],
                $value,
                $colors[$index % count($colors)]
            );
        }

        return view('livewire.charts.category-distribution-chart', [
            'columnChartModel' => $columnChartModel,
            'categories' => $categories,
            'totalItems' => $categories->sum('total_items'),
            'topCategory' => $categories->sortByDesc('total_items')->first(),
            'mostSuccessful' => $categories->sortByDesc('success_score')->first(),
            'timeRangeLabel' => $this->getTimeRangeLabel()
        ]);
    }

    protected function getSortColumn()
    {
        return match($this->sortBy) {
            'lost' => 'lost_count',
            'found' => 'found_count',
            'matched' => 'matched_count',
            'trend' => 'recent_items',
            default => 'total_items'
        };
    }

    protected function getChartValue($category)
    {
        return match($this->sortBy) {
            'lost' => $category['lost_count'],
            'found' => $category['found_count'],
            'matched' => $category['matched_count'],
            'trend' => $category['recent_items'],
            default => $category['total_items']
        };
    }

    protected function getTrendIndicator($recentItems, $totalItems)
    {
        $trend = ($recentItems / $totalItems) * 100;

        if ($trend >= 15) {
            return ['icon' => 'fas fa-arrow-up', 'color' => 'text-green-500', 'label' => 'High Activity'];
        } elseif ($trend >= 5) {
            return ['icon' => 'fas fa-arrow-right', 'color' => 'text-blue-500', 'label' => 'Steady'];
        } else {
            return ['icon' => 'fas fa-arrow-down', 'color' => 'text-gray-500', 'label' => 'Low Activity'];
        }
    }

    protected function calculateSuccessScore($category)
    {
        $matchRate = $category->matched_count > 0 ? ($category->matched_count / $category->total_items) * 100 : 0;
        $foundRate = $category->found_count > 0 ? ($category->found_count / $category->total_items) * 100 : 0;
        $avgMatchScore = $category->avg_match_score ?? 0;

        return round(($matchRate * 0.4) + ($foundRate * 0.3) + ($avgMatchScore * 0.3), 1);
    }

    protected function getTimeRangeLabel()
    {
        return match($this->timeRange) {
            'month' => 'This Month',
            'week' => 'This Week',
            '30days' => 'Last 30 Days',
            default => 'All Time'
        };
    }

    public function updatedTimeRange()
    {
        $this->emit('timeRangeChanged', $this->timeRange);
    }

    public function updatedSortBy()
    {
        $this->emit('sortByChanged', $this->sortBy);
    }

    public function onColumnClick($column)
    {
        $this->selectedCategory = $column['name'];
        $this->emit('categorySelected', [
            'category' => $column['name'],
            'value' => $column['value']
        ]);
    }
}
