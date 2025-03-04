<div class="bg-white p-6 rounded-lg shadow-sm">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 space-y-4 sm:space-y-0">
        <h3 class="text-lg font-medium text-gray-900">{{ $chartTitle }}</h3>
        <div class="flex flex-wrap items-center gap-4">
            <select wire:model="timeRange" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                <option value="all">All Time</option>
                <option value="month">This Month</option>
                <option value="week">This Week</option>
            </select>
            <select wire:model="sortBy" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                <option value="total">Sort by Total</option>
                <option value="reported">Sort by Reported</option>
                <option value="searched">Sort by Searched</option>
                <option value="found">Sort by Found</option>
                <option value="matched">Sort by Matched</option>
            </select>
            <select wire:model="limit" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                <option value="5">Top 5</option>
                <option value="10">Top 10</option>
                <option value="15">Top 15</option>
            </select>
        </div>
    </div>

    <!-- Overview Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-gradient-to-br from-blue-50 to-indigo-50 p-4 rounded-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Active Categories</p>
                    <h4 class="mt-1 text-2xl font-semibold text-gray-900">{{ $totalCategories }}</h4>
                </div>
                <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">
                    <i class="fas fa-folder text-blue-500"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-green-50 to-emerald-50 p-4 rounded-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Most Active Category</p>
                    <h4 class="mt-1 text-xl font-semibold text-gray-900">{{ $topCategory['name'] }}</h4>
                    <p class="text-sm text-gray-500">{{ $topCategory['total'] }} items</p>
                </div>
                <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center">
                    <i class="{{ $topCategory['icon'] }} text-green-500"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-purple-50 to-fuchsia-50 p-4 rounded-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Best Performance</p>
                    <h4 class="mt-1 text-xl font-semibold text-gray-900">{{ $mostSuccessful['name'] }}</h4>
                    <p class="text-sm text-gray-500">{{ $mostSuccessful['success_score'] }}% success rate</p>
                </div>
                <div class="w-10 h-10 rounded-full bg-purple-100 flex items-center justify-center">
                    <i class="fas fa-trophy text-purple-500"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart -->
    <div class="mb-6">
        <div class="bg-gray-50 rounded-lg p-4">
            <div wire:ignore class="h-[400px]">
                <livewire:livewire-column-chart
                    key="{{ $columnChartModel->reactiveKey() }}"
                    :column-chart-model="$columnChartModel"
                />
            </div>
        </div>
    </div>

    <!-- Detailed Stats Table -->
    <div class="mt-6">
        <h4 class="text-sm font-medium text-gray-900 mb-4">Category Performance Metrics</h4>
        <div class="overflow-x-auto rounded-lg border border-gray-200">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                        <th class="px-6 py-3 bg-gray-50 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                        <th class="px-6 py-3 bg-gray-50 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Match Rate</th>
                        <th class="px-6 py-3 bg-gray-50 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Success Score</th>
                        <th class="px-6 py-3 bg-gray-50 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Weekly Trend</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($categoryStats as $stat)
                        <tr @class([
                            'hover:bg-gray-50 transition-colors duration-150 ease-in-out',
                            'bg-blue-50' => $selectedCategory === $stat['name']
                        ])>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <i class="{{ $stat['icon'] }} text-gray-400 mr-2"></i>
                                    <span class="text-sm font-medium text-gray-900">{{ $stat['name'] }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-500">
                                {{ $stat['total'] }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <span class="text-sm {{ $stat['matched_rate'] >= 50 ? 'text-green-600' : 'text-gray-500' }}">
                                    {{ $stat['matched_rate'] }}%
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <div class="flex items-center justify-end">
                                    <div class="w-16 bg-gray-200 rounded-full h-2 mr-2">
                                        <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" style="width: {{ $stat['success_score'] }}%"></div>
                                    </div>
                                    <span class="text-sm text-gray-900">{{ $stat['success_score'] }}%</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <div class="flex items-center justify-end space-x-1">
                                    <i class="{{ $stat['trend_indicator']['icon'] }} {{ $stat['trend_indicator']['color'] }}"></i>
                                    <span class="text-sm {{ $stat['trend_indicator']['color'] }}">
                                        {{ $stat['weekly_trend'] }}%
                                    </span>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                No categories found with the current filters
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Legend -->
    <div class="mt-6 p-4 bg-gray-50 rounded-lg">
        <h4 class="text-sm font-medium text-gray-900 mb-3">Chart Legend</h4>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="flex items-center space-x-2">
                <span class="w-3 h-3 rounded-full bg-red-500"></span>
                <span class="text-sm text-gray-600">Reported Items</span>
            </div>
            <div class="flex items-center space-x-2">
                <span class="w-3 h-3 rounded-full bg-blue-500"></span>
                <span class="text-sm text-gray-600">Searched Items</span>
            </div>
            <div class="flex items-center space-x-2">
                <span class="w-3 h-3 rounded-full bg-green-500"></span>
                <span class="text-sm text-gray-600">Found Items</span>
            </div>
        </div>
    </div>
</div>
