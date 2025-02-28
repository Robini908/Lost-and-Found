<div class="bg-white p-6 rounded-lg shadow-sm">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 space-y-4 sm:space-y-0">
        <h3 class="text-lg font-medium text-gray-900">Category Analysis</h3>
        <div class="flex items-center space-x-4">
            <select wire:model="sortBy" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                <option value="total">Sort by Total Items</option>
                <option value="reported">Sort by Reported Items</option>
                <option value="searched">Sort by Searched Items</option>
                <option value="found">Sort by Found Items</option>
                <option value="matched">Sort by Matched Items</option>
            </select>
            <select wire:model="limit" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                <option value="5">Top 5</option>
                <option value="10">Top 10</option>
                <option value="15">Top 15</option>
            </select>
        </div>
    </div>

    <div class="mb-6">
        <div wire:ignore>
            <livewire:livewire-column-chart
                :column-chart-model="$columnChartModel"
            />
        </div>
    </div>

    <div class="mt-6">
        <h4 class="text-sm font-medium text-gray-900 mb-4">Category Success Metrics</h4>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                        <th class="px-6 py-3 bg-gray-50 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total Items</th>
                        <th class="px-6 py-3 bg-gray-50 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Match Rate</th>
                        <th class="px-6 py-3 bg-gray-50 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Claim Rate</th>
                        <th class="px-6 py-3 bg-gray-50 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Verification Rate</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($categoryStats as $stat)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $stat['name'] }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-500">
                                {{ $stat['total'] }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <span class="text-sm {{ $stat['matched_rate'] >= 50 ? 'text-green-600' : 'text-gray-500' }}">
                                    {{ $stat['matched_rate'] }}%
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <span class="text-sm {{ $stat['claimed_rate'] >= 50 ? 'text-green-600' : 'text-gray-500' }}">
                                    {{ $stat['claimed_rate'] }}%
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <span class="text-sm {{ $stat['verification_rate'] >= 50 ? 'text-green-600' : 'text-gray-500' }}">
                                    {{ $stat['verification_rate'] }}%
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4 grid grid-cols-1 sm:grid-cols-3 gap-4">
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
