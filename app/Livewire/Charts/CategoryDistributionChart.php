<?php

namespace App\Livewire\Charts;

use Livewire\Component;
use App\Models\LostItem;
use App\Models\Category;
use Asantibanez\LivewireCharts\Models\ColumnChartModel;
use Illuminate\Support\Facades\DB;

class CategoryDistributionChart extends Component
{
    public $sortBy = 'total'; // total, reported, searched, found, matched
    public $limit = 10;

    protected $queryString = ['sortBy', 'limit'];

    public function render()
    {
        $categories = Category::withCount([
                'lostItems as total_count',
                'lostItems as reported_count' => function ($query) {
                    $query->where('item_type', 'reported');
                },
                'lostItems as searched_count' => function ($query) {
                    $query->where('item_type', 'searched');
                },
                'lostItems as found_count' => function ($query) {
                    $query->where('item_type', 'found');
                },
                'lostItems as matched_count' => function ($query) {
                    $query->whereNotNull('matched_found_item_id');
                },
                'lostItems as claimed_count' => function ($query) {
                    $query->whereNotNull('claimed_by');
                },
                'lostItems as verified_count' => function ($query) {
                    $query->where('is_verified', true);
                }
            ])
            ->having('total_count', '>', 0)
            ->orderByDesc($this->sortBy . '_count')
            ->limit($this->limit)
            ->get();

        $columnChartModel = (new ColumnChartModel())
            ->setTitle('Items by Category')
            ->setAnimated(true)
            ->setLegendVisibility(true)
            ->setDataLabelsEnabled(true)
            ->withOnColumnClickEventName('onColumnClick');

        foreach ($categories as $category) {
            // Add reported items (red)
            $columnChartModel->addColumn(
                $category->name,
                $category->reported_count,
                '#EF4444',
                ['category_id' => $category->id, 'type' => 'reported']
            );

            // Add searched items (blue)
            $columnChartModel->addColumn(
                $category->name,
                $category->searched_count,
                '#3B82F6',
                ['category_id' => $category->id, 'type' => 'searched']
            );

            // Add found items (green)
            $columnChartModel->addColumn(
                $category->name,
                $category->found_count,
                '#10B981',
                ['category_id' => $category->id, 'type' => 'found']
            );
        }

        // Calculate success metrics
        $categoryStats = $categories->map(function ($category) {
            return [
                'name' => $category->name,
                'total' => $category->total_count,
                'matched_rate' => $category->total_count > 0
                    ? round(($category->matched_count / $category->total_count) * 100, 1)
                    : 0,
                'claimed_rate' => $category->total_count > 0
                    ? round(($category->claimed_count / $category->total_count) * 100, 1)
                    : 0,
                'verification_rate' => $category->total_count > 0
                    ? round(($category->verified_count / $category->total_count) * 100, 1)
                    : 0
            ];
        });

        return view('livewire.charts.category-distribution-chart', [
            'columnChartModel' => $columnChartModel,
            'categoryStats' => $categoryStats
        ]);
    }

    public function onColumnClick($column)
    {
        $this->emit('categorySelected', [
            'category_id' => $column['extras']['category_id'],
            'type' => $column['extras']['type']
        ]);
    }
}
