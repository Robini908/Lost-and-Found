<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
    <!-- Lost Items -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow duration-200">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">Lost Items</p>
                <h3 class="text-2xl font-semibold text-gray-900 mt-1">{{ $stats['totalLostItems'] }}</h3>
            </div>
            <div class="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center">
                <i class="fas fa-search text-blue-500"></i>
            </div>
        </div>
        <div class="mt-4 flex items-center text-sm">
            <span class="text-gray-500">Last 30 days:</span>
            <span class="ml-2 font-medium text-gray-900">{{ $stats['lastMonthLostItems'] }}</span>
        </div>
    </div>

    <!-- Found Items -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow duration-200">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">Found Items</p>
                <h3 class="text-2xl font-semibold text-gray-900 mt-1">{{ $stats['totalFoundItems'] }}</h3>
            </div>
            <div class="w-10 h-10 rounded-full bg-green-50 flex items-center justify-center">
                <i class="fas fa-box text-green-500"></i>
            </div>
        </div>
        <div class="mt-4 flex items-center text-sm">
            <span class="text-gray-500">Last 30 days:</span>
            <span class="ml-2 font-medium text-gray-900">{{ $stats['lastMonthFoundItems'] }}</span>
        </div>
    </div>

    <!-- Successful Matches -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow duration-200">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">Successful Matches</p>
                <h3 class="text-2xl font-semibold text-gray-900 mt-1">{{ $stats['successfulMatches'] }}</h3>
            </div>
            <div class="w-10 h-10 rounded-full bg-purple-50 flex items-center justify-center">
                <i class="fas fa-check-circle text-purple-500"></i>
            </div>
        </div>
        <div class="mt-4 flex items-center text-sm">
            <span class="text-gray-500">Success rate:</span>
            <span class="ml-2 font-medium text-gray-900">{{ $stats['matchRate'] }}</span>
        </div>
    </div>

    <!-- Recovery Rate -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow duration-200">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">Recovery Rate</p>
                <h3 class="text-2xl font-semibold text-gray-900 mt-1">{{ $stats['recoveryRate'] }}</h3>
            </div>
            <div class="w-10 h-10 rounded-full bg-indigo-50 flex items-center justify-center">
                <i class="fas fa-chart-line text-indigo-500"></i>
            </div>
        </div>
        <div class="mt-4 flex items-center text-sm">
            <span class="text-gray-500">Trend:</span>
            <span class="ml-2 font-medium text-gray-900">{{ $stats['recoveryTrend'] }}</span>
            @if($stats['recoveryTrend'] === 'Increasing')
                <i class="fas fa-arrow-up text-green-500 ml-1"></i>
            @elseif($stats['recoveryTrend'] === 'Decreasing')
                <i class="fas fa-arrow-down text-red-500 ml-1"></i>
            @else
                <i class="fas fa-minus text-gray-500 ml-1"></i>
            @endif
        </div>
    </div>
</div>
