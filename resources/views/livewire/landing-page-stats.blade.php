<div wire:poll.60s class="relative p-6 overflow-hidden">
    <!-- Background Gradient Orbs -->
    <div class="absolute top-0 left-0 w-96 h-96 bg-blue-400 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob"></div>
    <div class="absolute top-0 right-0 w-96 h-96 bg-purple-400 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-2000"></div>
    <div class="absolute -bottom-8 left-20 w-96 h-96 bg-pink-400 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-4000"></div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 relative">
        <!-- Match Success Rate -->
        <div class="backdrop-blur-lg bg-white/30 rounded-2xl p-6 shadow-[0_8px_30px_rgb(0,0,0,0.12)]
                    border border-white/20 hover:shadow-[0_8px_30px_rgb(0,0,0,0.16)] transition-all duration-300
                    hover:translate-y-[-2px]"
             x-data="{ shown: false }"
             x-intersect="shown = true">
            <div class="relative">
                <!-- Loading Indicator -->
                <div wire:loading class="absolute inset-0 flex items-center justify-center bg-white/50 rounded-xl backdrop-blur-sm">
                    <div class="animate-spin rounded-full h-8 w-8 border-2 border-blue-600 border-t-transparent"></div>
                </div>

                <!-- Icon -->
                <div class="absolute -top-4 -right-4 w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg rotate-12 flex items-center justify-center">
                    <i class="fas fa-chart-line text-white text-2xl -rotate-12"></i>
                </div>

                <!-- Content -->
                <div class="pt-2">
                    <div class="text-4xl font-bold text-gray-800 mb-2 font-primary tracking-tight"
                         x-show="shown"
                         x-transition:enter="transition ease-out duration-1000"
                         x-transition:enter-start="opacity-0 transform translate-y-4"
                         x-transition:enter-end="opacity-100 transform translate-y-0">
                        <span class="inline-block" x-data="{ value: 0 }"
                              x-init="$nextTick(() => { setTimeout(() => { value = {{ $stats['matchRate'] }} }, 400) })"
                              x-text="Math.round(value) + '%'"></span>
                    </div>
                    <div class="text-gray-600 font-secondary">Match Success Rate</div>
                    <div class="mt-2 text-sm text-gray-500">Of Reported Items</div>
                </div>
            </div>
        </div>

        <!-- Average Recovery Time -->
        <div class="backdrop-blur-lg bg-white/30 rounded-2xl p-6 shadow-[0_8px_30px_rgb(0,0,0,0.12)]
                    border border-white/20 hover:shadow-[0_8px_30px_rgb(0,0,0,0.16)] transition-all duration-300
                    hover:translate-y-[-2px]"
             x-data="{ shown: false }"
             x-intersect="shown = true">
            <div class="relative">
                <!-- Loading Indicator -->
                <div wire:loading class="absolute inset-0 flex items-center justify-center bg-white/50 rounded-xl backdrop-blur-sm">
                    <div class="animate-spin rounded-full h-8 w-8 border-2 border-green-600 border-t-transparent"></div>
                </div>

                <!-- Icon -->
                <div class="absolute -top-4 -right-4 w-16 h-16 bg-gradient-to-br from-green-500 to-green-600 rounded-lg rotate-12 flex items-center justify-center">
                    <i class="fas fa-clock text-white text-2xl -rotate-12"></i>
                </div>

                <!-- Content -->
                <div class="pt-2">
                    <div class="text-4xl font-bold text-gray-800 mb-2 font-primary tracking-tight"
                         x-show="shown"
                         x-transition:enter="transition ease-out duration-1000">
                        <span class="inline-block" x-data="{ value: 0 }"
                              x-init="$nextTick(() => { setTimeout(() => { value = {{ $stats['avgRecoveryTime'] }} }, 400) })"
                              x-text="Math.round(value)"></span>
                        <span class="text-2xl">hrs</span>
                    </div>
                    <div class="text-gray-600 font-secondary">Average Recovery</div>
                    <div class="mt-2 text-sm text-gray-500">Time to Match</div>
                </div>
            </div>
        </div>

        <!-- Items Statistics -->
        <div class="backdrop-blur-lg bg-white/30 rounded-2xl p-6 shadow-[0_8px_30px_rgb(0,0,0,0.12)]
                    border border-white/20 hover:shadow-[0_8px_30px_rgb(0,0,0,0.16)] transition-all duration-300
                    hover:translate-y-[-2px]"
             x-data="{ shown: false }"
             x-intersect="shown = true">
            <div class="relative">
                <!-- Loading Indicator -->
                <div wire:loading class="absolute inset-0 flex items-center justify-center bg-white/50 rounded-xl backdrop-blur-sm">
                    <div class="animate-spin rounded-full h-8 w-8 border-2 border-purple-600 border-t-transparent"></div>
                </div>

                <!-- Icon -->
                <div class="absolute -top-4 -right-4 w-16 h-16 bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg rotate-12 flex items-center justify-center">
                    <i class="fas fa-handshake text-white text-2xl -rotate-12"></i>
                </div>

                <!-- Content -->
                <div class="pt-2">
                    <div class="text-4xl font-bold text-gray-800 mb-2 font-primary tracking-tight"
                         x-show="shown"
                         x-transition:enter="transition ease-out duration-1000">
                        <span class="inline-block" x-data="{ value: 0 }"
                              x-init="$nextTick(() => { setTimeout(() => { value = {{ $stats['totalItems']['matched'] }} }, 400) })"
                              x-text="value"></span>
                    </div>
                    <div class="text-gray-600 font-secondary">Successful Matches</div>
                    <div class="mt-2 text-sm text-gray-500 space-x-2">
                        <span class="inline-flex items-center px-2 py-1 rounded-full bg-blue-100 text-blue-800">
                            <i class="fas fa-search text-xs mr-1"></i>
                            {{ $stats['totalItems']['reported'] }} Reported
                        </span>
                        <span class="inline-flex items-center px-2 py-1 rounded-full bg-green-100 text-green-800">
                            <i class="fas fa-hand-holding text-xs mr-1"></i>
                            {{ $stats['totalItems']['found'] }} Found
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="backdrop-blur-lg bg-white/30 rounded-2xl p-6 shadow-[0_8px_30px_rgb(0,0,0,0.12)]
                    border border-white/20 hover:shadow-[0_8px_30px_rgb(0,0,0,0.16)] transition-all duration-300
                    hover:translate-y-[-2px]"
             x-data="{ shown: false }"
             x-intersect="shown = true">
            <div class="relative">
                <!-- Loading Indicator -->
                <div wire:loading class="absolute inset-0 flex items-center justify-center bg-white/50 rounded-xl backdrop-blur-sm">
                    <div class="animate-spin rounded-full h-8 w-8 border-2 border-amber-600 border-t-transparent"></div>
                </div>

                <!-- Icon -->
                <div class="absolute -top-4 -right-4 w-16 h-16 bg-gradient-to-br from-amber-500 to-amber-600 rounded-lg rotate-12 flex items-center justify-center">
                    <i class="fas fa-bolt text-white text-2xl -rotate-12"></i>
                </div>

                <!-- Content -->
                <div class="pt-2">
                    <div class="text-4xl font-bold text-gray-800 mb-2 font-primary tracking-tight"
                         x-show="shown"
                         x-transition:enter="transition ease-out duration-1000">
                        <span class="inline-block" x-data="{ value: 0 }"
                              x-init="$nextTick(() => { setTimeout(() => { value = {{ $stats['recentActivity'] }} }, 400) })"
                              x-text="value"></span>+
                    </div>
                    <div class="text-gray-600 font-secondary">Recent Items</div>
                    <div class="mt-2 text-sm text-gray-500">Last 7 Days</div>
                </div>
            </div>
        </div>
    </div>
</div>
