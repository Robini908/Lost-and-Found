<div>
    <div class="flex justify-between items-center mb-6">
        <h3 class="text-lg font-semibold text-gray-900">Item Status Distribution</h3>
        <div class="flex items-center space-x-2">
            <span class="text-sm text-gray-500">Total Items: {{ $totalItems }}</span>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl p-4 border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Total Items</p>
                    <p class="text-2xl font-semibold">{{ $totalItems }}</p>
                </div>
                <div class="w-10 h-10 rounded-full bg-gray-50 flex items-center justify-center">
                    <i class="fas fa-box text-gray-400"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-4 border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Reported</p>
                    <p class="text-2xl font-semibold">{{ $reportedCount }}</p>
                </div>
                <div class="w-10 h-10 rounded-full bg-red-50 flex items-center justify-center">
                    <i class="fas fa-exclamation-circle text-red-400"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-4 border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Searched</p>
                    <p class="text-2xl font-semibold">{{ $searchedCount }}</p>
                </div>
                <div class="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center">
                    <i class="fas fa-search text-blue-400"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-4 border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Found</p>
                    <p class="text-2xl font-semibold">{{ $foundCount }}</p>
                </div>
                <div class="w-10 h-10 rounded-full bg-green-50 flex items-center justify-center">
                    <i class="fas fa-box text-green-400"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl p-6 border border-gray-100">
        <livewire:livewire-pie-chart
            :pie-chart-model="$pieChartModel"
        />
    </div>
</div>
