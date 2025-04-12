<div>
    <div class="flex justify-between items-center mb-6">
        <h3 class="text-lg font-semibold text-gray-900">Category Distribution</h3>
        <div class="flex items-center space-x-2">
            <span class="text-sm text-gray-500">Total Items: {{ $totalItems }}</span>
        </div>
    </div>

    <!-- Category Cards Grid -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 mb-6">
        @foreach($categories->take(5) as $category)
            <div class="bg-white rounded-xl p-4 border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">{{ $category['name'] }}</p>
                        <p class="text-2xl font-semibold">{{ $category['total_items'] }}</p>
                    </div>
                    <div class="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center">
                        <i class="{{ $category['icon'] ?? 'fas fa-folder' }} text-blue-400"></i>
                    </div>
                </div>
                <div class="mt-2">
                    <div class="text-xs text-gray-500">
                        {{ number_format(($category['total_items'] / $totalItems) * 100, 1) }}% of total
                    </div>
                    <div class="flex items-center mt-1">
                        <i class="{{ $category['trend_indicator']['icon'] }} {{ $category['trend_indicator']['color'] }} text-xs mr-1"></i>
                        <span class="text-xs {{ $category['trend_indicator']['color'] }}">{{ $category['trend_indicator']['label'] }}</span>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Column Chart -->
    <div class="bg-white rounded-xl p-6 border border-gray-100">
        <div class="h-[400px]">
            <livewire:livewire-column-chart
                :column-chart-model="$columnChartModel"
            />
        </div>
    </div>
</div>
