<!-- System-wide Stats Section -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Lost Items -->
    <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 rounded-full bg-red-50 flex items-center justify-center">
                <i class="fas fa-search text-red-500 text-xl"></i>
            </div>
            <span class="px-3 py-1 text-xs font-medium bg-red-50 text-red-600 rounded-full">System Wide</span>
        </div>
        <h3 class="text-2xl font-bold text-gray-900">{{ $totalLostItems }}</h3>
        <p class="text-sm text-gray-500 mt-1">Lost Items</p>
        <div class="mt-2 flex items-center text-xs text-gray-500">
            <i class="fas fa-clock mr-1.5"></i>
            <span>Last 30 days: {{ $last30DaysLostItems }}</span>
        </div>
    </div>

    <!-- Found Items -->
    <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 rounded-full bg-green-50 flex items-center justify-center">
                <i class="fas fa-box text-green-500 text-xl"></i>
            </div>
            <span class="px-3 py-1 text-xs font-medium bg-green-50 text-green-600 rounded-full">System Wide</span>
        </div>
        <h3 class="text-2xl font-bold text-gray-900">{{ $totalFoundItems }}</h3>
        <p class="text-sm text-gray-500 mt-1">Found Items</p>
        <div class="mt-2 flex items-center text-xs text-gray-500">
            <i class="fas fa-clock mr-1.5"></i>
            <span>Last 30 days: {{ $last30DaysFoundItems }}</span>
        </div>
    </div>

    <!-- Successful Matches -->
    <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 rounded-full bg-blue-50 flex items-center justify-center">
                <i class="fas fa-handshake text-blue-500 text-xl"></i>
            </div>
            <span class="px-3 py-1 text-xs font-medium bg-blue-50 text-blue-600 rounded-full">System Wide</span>
        </div>
        <h3 class="text-2xl font-bold text-gray-900">{{ $successfulMatches }}</h3>
        <p class="text-sm text-gray-500 mt-1">Successful Matches</p>
        <div class="mt-2 flex items-center text-xs text-gray-500">
            <i class="fas fa-chart-line mr-1.5"></i>
            <span>High confidence matches (≥70%)</span>
        </div>
    </div>

    <!-- Recovery Rate -->
    <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 rounded-full bg-purple-50 flex items-center justify-center">
                <i class="fas fa-percentage text-purple-500 text-xl"></i>
            </div>
            <span class="px-3 py-1 text-xs font-medium bg-purple-50 text-purple-600 rounded-full">System Wide</span>
        </div>
        <h3 class="text-2xl font-bold text-gray-900">{{ number_format($recoveryRate, 1) }}%</h3>
        <p class="text-sm text-gray-500 mt-1">Recovery Rate</p>
        <div class="mt-2 flex items-center text-xs text-gray-500">
            <i class="fas fa-chart-pie mr-1.5"></i>
            <span>Lost items successfully matched</span>
        </div>
    </div>
</div>
