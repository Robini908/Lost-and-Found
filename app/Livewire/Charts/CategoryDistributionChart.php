<?php

namespace App\Livewire\Charts;

use Livewire\Component;
use App\Models\LostItem;
use App\Models\Category;
use Asantibanez\LivewireCharts\Models\ColumnChartModel;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CategoryDistributionChart extends Component
{
    public $sortBy = 'total';
    public $limit = 10;
    public $timeRange = 'all'; // all, month, week
    public $selectedCategory = null;

    protected $queryString = ['sortBy', 'limit', 'timeRange'];

    public function mount()
    {
        $this->sortBy = 'total';
    }

    public function render()
    {
        $query = Category::query();

        // Apply time range filter
        if ($this->timeRange !== 'all') {
            $startDate = $this->timeRange === 'month'
                ? Carbon::now()->startOfMonth()
                : Carbon::now()->startOfWeek();

            $query->whereHas('lostItems', function ($q) use ($startDate) {
                $q->where('created_at', '>=', $startDate);
            });
        }

        // Get categories with counts using a single query
        $categories = $query->select('categories.*')
            ->selectRaw('
                COUNT(lost_items.id) as total_count,
                SUM(CASE WHEN lost_items.item_type = ? THEN 1 ELSE 0 END) as reported_count,
                SUM(CASE WHEN lost_items.item_type = ? THEN 1 ELSE 0 END) as searched_count,
                SUM(CASE WHEN lost_items.item_type = ? THEN 1 ELSE 0 END) as found_count,
                SUM(CASE WHEN lost_items.matched_found_item_id IS NOT NULL THEN 1 ELSE 0 END) as matched_count,
                SUM(CASE WHEN lost_items.claimed_by IS NOT NULL THEN 1 ELSE 0 END) as claimed_count,
                SUM(CASE WHEN lost_items.is_verified = true THEN 1 ELSE 0 END) as verified_count,
                SUM(CASE WHEN lost_items.created_at >= ? THEN 1 ELSE 0 END) as recent_count
            ', [
                LostItem::TYPE_REPORTED,
                LostItem::TYPE_SEARCHED,
                LostItem::TYPE_FOUND,
                now()->subDays(7)
            ])
            ->leftJoin('lost_items', 'categories.id', '=', 'lost_items.category_id')
            ->groupBy('categories.id')
            ->having('total_count', '>', 0)
            ->orderByRaw("CASE
                WHEN ? = 'total' THEN total_count
                WHEN ? = 'reported' THEN reported_count
                WHEN ? = 'searched' THEN searched_count
                WHEN ? = 'found' THEN found_count
                WHEN ? = 'matched' THEN matched_count
                ELSE total_count
            END DESC", [
                $this->sortBy, $this->sortBy, $this->sortBy, $this->sortBy, $this->sortBy
            ])
            ->limit($this->limit)
            ->get();

        // Prepare chart model with improved configuration
        $columnChartModel = (new ColumnChartModel())
            ->setTitle($this->getChartTitle())
            ->setAnimated(true)
            ->multiColumn()
            ->setOpacity(0.9)
            ->withOnColumnClickEventName('onColumnClick');

        // Set colors for each series
        $colors = [
            'Reported' => '#EF4444',
            'Searched' => '#3B82F6',
            'Found' => '#10B981'
        ];

        // Add data to chart with proper grouping
        foreach ($categories as $category) {
            if ($category->total_count > 0) {
            $columnChartModel->addColumn(
                $category->name,
                    [
                        'Reported' => $category->reported_count,
                        'Searched' => $category->searched_count,
                        'Found' => $category->found_count
                    ],
                    $colors
                );
            }
        }

        // Calculate success metrics and trends
        $categoryStats = $categories->map(function ($category) {
            $matchRate = $category->total_count > 0
                ? round(($category->matched_count / $category->total_count) * 100, 1)
                : 0;

            $claimRate = $category->total_count > 0
                ? round(($category->claimed_count / $category->total_count) * 100, 1)
                : 0;

            $verificationRate = $category->total_count > 0
                ? round(($category->verified_count / $category->total_count) * 100, 1)
                : 0;

            $weeklyTrend = $category->total_count > 0
                ? round(($category->recent_count / $category->total_count) * 100, 1)
                : 0;

            return [
                'name' => $category->name,
                'icon' => $category->icon ?? 'fas fa-folder',
                'total' => $category->total_count,
                'reported' => $category->reported_count,
                'searched' => $category->searched_count,
                'found' => $category->found_count,
                'matched_rate' => $matchRate,
                'claimed_rate' => $claimRate,
                'verification_rate' => $verificationRate,
                'weekly_trend' => $weeklyTrend,
                'trend_indicator' => $this->getTrendIndicator($weeklyTrend),
                'success_score' => round(($matchRate + $claimRate + $verificationRate) / 3, 1)
            ];
        });

        $topCategory = $categoryStats->sortByDesc('total')->first();
        $mostSuccessful = $categoryStats->sortByDesc('success_score')->first();

        return view('livewire.charts.category-distribution-chart', [
            'columnChartModel' => $columnChartModel,
            'categoryStats' => $categoryStats,
            'totalCategories' => $categories->count(),
            'topCategory' => $topCategory ?? ['name' => 'None', 'total' => 0, 'icon' => 'fas fa-folder'],
            'mostSuccessful' => $mostSuccessful ?? ['name' => 'None', 'success_score' => 0],
            'chartTitle' => $this->getChartTitle()
        ]);
    }

    protected function getTrendIndicator($weeklyTrend)
    {
        if ($weeklyTrend >= 10) {
            return ['icon' => 'fas fa-arrow-up', 'color' => 'text-green-500'];
        } elseif ($weeklyTrend <= -10) {
            return ['icon' => 'fas fa-arrow-down', 'color' => 'text-red-500'];
        }
        return ['icon' => 'fas fa-arrows-h', 'color' => 'text-gray-500'];
    }

    protected function getChartTitle()
    {
        $timeRange = match($this->timeRange) {
            'month' => 'This Month',
            'week' => 'This Week',
            default => 'All Time'
        };

        return "Category Distribution - {$timeRange}";
    }

    public function onColumnClick($column)
    {
        $this->selectedCategory = $column['name'];
        $this->emit('categorySelected', [
            'category_name' => $column['name'],
            'value' => $column['value']
        ]);
    }
}
