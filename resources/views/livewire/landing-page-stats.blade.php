<div class="py-12">
    <!-- Main Stats Container -->
    <div class="relative bg-white/80 backdrop-blur-sm rounded-3xl shadow-xl border border-white/20 p-8">
        <!-- Primary Stats -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-12">
            <!-- Total Items -->
            <div class="relative group">
                <div class="absolute inset-0 bg-gradient-to-r from-blue-500/10 to-indigo-500/10 rounded-2xl transform transition-transform group-hover:scale-105 duration-300"></div>
                <div class="relative p-6 text-center">
                    <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center transform transition-transform group-hover:rotate-12 duration-300">
                        <i class="fas fa-box-open text-white text-2xl"></i>
                    </div>
                    <h3 class="text-4xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 text-transparent bg-clip-text mb-2">
                        {{ number_format($totalLostItems + $totalFoundItems) }}
                    </h3>
                    <p class="text-gray-600 font-medium mb-3">Total Items</p>
                    <div class="flex justify-center gap-4 text-sm">
                        <span class="text-blue-600">Lost: {{ number_format($totalLostItems) }}</span>
                        <span class="text-indigo-600">Found: {{ number_format($totalFoundItems) }}</span>
                    </div>
                </div>
            </div>

            <!-- Success Rate -->
            <div class="relative group">
                <div class="absolute inset-0 bg-gradient-to-r from-green-500/10 to-emerald-500/10 rounded-2xl transform transition-transform group-hover:scale-105 duration-300"></div>
                <div class="relative p-6 text-center">
                    <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-gradient-to-br from-green-500 to-emerald-600 flex items-center justify-center transform transition-transform group-hover:rotate-12 duration-300">
                        <i class="fas fa-chart-line text-white text-2xl"></i>
                    </div>
                    <h3 class="text-4xl font-bold bg-gradient-to-r from-green-600 to-emerald-600 text-transparent bg-clip-text mb-2">
                        {{ number_format($recoveryRate, 1) }}%
                    </h3>
                    <p class="text-gray-600 font-medium mb-3">Success Rate</p>
                    <p class="text-sm text-green-600">{{ number_format($successfulMatches) }} matches</p>
                </div>
            </div>

            <!-- Match Speed -->
            <div class="relative group">
                <div class="absolute inset-0 bg-gradient-to-r from-purple-500/10 to-pink-500/10 rounded-2xl transform transition-transform group-hover:scale-105 duration-300"></div>
                <div class="relative p-6 text-center">
                    <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-gradient-to-br from-purple-500 to-pink-600 flex items-center justify-center transform transition-transform group-hover:rotate-12 duration-300">
                        <i class="fas fa-bolt text-white text-2xl"></i>
                    </div>
                    <h3 class="text-4xl font-bold bg-gradient-to-r from-purple-600 to-pink-600 text-transparent bg-clip-text mb-2">
                        @if(str_contains($averageMatchTime, 'Lightning'))
                            ⚡
                        @else
                            {{ preg_replace('/(\d+)/', '$1', $averageMatchTime) }}
                        @endif
                    </h3>
                    <p class="text-gray-600 font-medium mb-3">Average Match Time</p>
                    <p class="text-sm text-purple-600">
                        @if(str_contains($averageMatchTime, 'Lightning'))
                            Lightning Fast
                        @else
                            {{ preg_replace('/[\d\s]+(.+)/', '$1', $averageMatchTime) }}
                        @endif
                    </p>
                </div>
            </div>

            <!-- Active Users -->
            <div class="relative group">
                <div class="absolute inset-0 bg-gradient-to-r from-amber-500/10 to-orange-500/10 rounded-2xl transform transition-transform group-hover:scale-105 duration-300"></div>
                <div class="relative p-6 text-center">
                    <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-gradient-to-br from-amber-500 to-orange-600 flex items-center justify-center transform transition-transform group-hover:rotate-12 duration-300">
                        <i class="fas fa-users text-white text-2xl"></i>
                    </div>
                    <h3 class="text-4xl font-bold bg-gradient-to-r from-amber-600 to-orange-600 text-transparent bg-clip-text mb-2">
                        {{ number_format($activeUsers) }}
                    </h3>
                    <p class="text-gray-600 font-medium mb-3">Active Users</p>
                    <p class="text-sm text-amber-600">This Month</p>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="text-center">
            <div class="inline-flex items-center px-4 py-2 rounded-full bg-gradient-to-r from-blue-100 to-indigo-100 text-gray-700">
                <i class="fas fa-clock text-blue-600 mr-2"></i>
                <span>Last 30 Days: <strong>{{ number_format($last30DaysLostItems + $last30DaysFoundItems) }}</strong> new items</span>
            </div>
        </div>
    </div>

    <!-- Additional Stats -->
    <div class="mt-8 grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Community Impact -->
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl p-6 text-white">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center">
                    <i class="fas fa-heart text-white text-xl"></i>
                </div>
                <div>
                    <h4 class="text-lg font-semibold">Community Impact</h4>
                    <p class="text-2xl font-bold">{{ number_format($successfulMatches) }} Items Returned</p>
                </div>
            </div>
        </div>
    </div>
</div>
