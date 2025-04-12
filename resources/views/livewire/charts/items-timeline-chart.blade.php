<div>
    <div class="flex justify-between items-center mb-6">
        <h3 class="text-lg font-semibold text-gray-900">Items by Status Over Time</h3>
        <div class="flex items-center space-x-4">
            <select wire:model="timeRange" class="form-select rounded-lg text-sm border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                <option value="last_7_days">Last 7 Days</option>
                <option value="last_30_days">Last 30 Days</option>
                <option value="last_90_days">Last 90 Days</option>
                <option value="last_year">Last Year</option>
            </select>
        </div>
    </div>

    <div class="bg-white rounded-xl p-6 border border-gray-100">
        <div class="h-[400px]">
            <livewire:livewire-line-chart
                :line-chart-model="$lineChartModel"
            />
        </div>

        <!-- Legend -->
        <div class="flex justify-center items-center space-x-6 mt-6">
            <div class="flex items-center">
                <span class="w-3 h-3 rounded-full bg-red-500 mr-2"></span>
                <span class="text-sm text-gray-600">Reported</span>
            </div>
            <div class="flex items-center">
                <span class="w-3 h-3 rounded-full bg-blue-500 mr-2"></span>
                <span class="text-sm text-gray-600">Searched</span>
            </div>
            <div class="flex items-center">
                <span class="w-3 h-3 rounded-full bg-green-500 mr-2"></span>
                <span class="text-sm text-gray-600">Found</span>
            </div>
        </div>
    </div>
</div>
