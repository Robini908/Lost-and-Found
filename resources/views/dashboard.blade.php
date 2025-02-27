<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0">
            <div>
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Welcome back') }}, {{ Auth::user()->name }}!
        </h2>
                <p class="text-sm text-gray-600 mt-1">Here's what's happening with your lost and found items</p>
            </div>
            <div class="flex flex-col sm:flex-row gap-3">
                <x-button href="{{ route('products.report-item') }}" class="bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white shadow-lg hover:shadow-xl transition-all duration-200">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    {{ __('Report Lost Item') }}
                </x-button>
                <x-button href="{{ route('products.report-found-item') }}" class="bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white shadow-lg hover:shadow-xl transition-all duration-200">
                    <i class="fas fa-search mr-2"></i>
                    {{ __('Report Found Item') }}
                </x-button>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Stats Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                <!-- Total Lost Items -->
                <div class="transform hover:scale-105 transition-transform duration-200">
                    <x-stats-card
                        title="Lost Items"
                        :value="$stats['totalLostItems'] ?? 0"
                        icon="fa-search"
                        gradient-from="from-pink-500"
                        gradient-to="to-rose-600"
                        :subtitle="'Last 30 days: ' . ($stats['lastMonthLostItems'] ?? 0)" />
                </div>

                <!-- Total Found Items -->
                <div class="transform hover:scale-105 transition-transform duration-200">
                    <x-stats-card
                        title="Found Items"
                        :value="$stats['totalFoundItems'] ?? 0"
                        icon="fa-box"
                        gradient-from="from-emerald-500"
                        gradient-to="to-teal-600"
                        :subtitle="'Last 30 days: ' . ($stats['lastMonthFoundItems'] ?? 0)" />
                </div>

                <!-- Successful Matches -->
                <div class="transform hover:scale-105 transition-transform duration-200">
                    <x-stats-card
                        title="Successful Matches"
                        :value="$stats['successfulMatches'] ?? 0"
                        icon="fa-check-circle"
                        gradient-from="from-blue-500"
                        gradient-to="to-indigo-600"
                        :subtitle="'Success rate: ' . ($stats['matchRate'] ?? '0%')" />
                </div>

                <!-- Recovery Rate -->
                <div class="transform hover:scale-105 transition-transform duration-200">
                    <x-stats-card
                        title="Recovery Rate"
                        :value="$stats['recoveryRate'] ?? '0%'"
                        icon="fa-chart-line"
                        gradient-from="from-violet-500"
                        gradient-to="to-purple-600"
                        :subtitle="'Trend: ' . ($stats['recoveryTrend'] ?? 'Stable')" />
                </div>
            </div>

            <!-- Main Content Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Quick Actions Section -->
                <div class="lg:col-span-2">
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex justify-between items-center mb-6">
                                <h3 class="text-lg font-medium text-gray-900">Quick Actions</h3>
                                <a href="{{ route('products.view-items') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                    View All <i class="fas fa-arrow-right ml-1"></i>
                                </a>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach($recentItems ?? [] as $item)
                                    <div class="bg-gray-50 rounded-lg p-4 hover:bg-gray-100 transition-colors duration-200">
                                        <div class="flex items-center">
                                            @if($item->images->isNotEmpty())
                                                <img src="{{ Storage::url($item->images->first()->image_path) }}"
                                                     alt="{{ $item->title }}"
                                                     class="w-16 h-16 rounded-lg object-cover">
                                            @else
                                                <div class="w-16 h-16 rounded-lg bg-gray-200 flex items-center justify-center">
                                                    <i class="fas fa-image text-gray-400 text-2xl"></i>
                                                </div>
                                            @endif
                                            <div class="ml-4 flex-1">
                                                <h4 class="text-sm font-medium text-gray-900">{{ $item->title }}</h4>
                                                <p class="text-sm text-gray-500 truncate">{{ $item->description }}</p>
                                                <div class="mt-2">
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $item->item_type === 'lost' ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                                                        {{ ucfirst($item->item_type) }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity Section -->
                <div class="lg:col-span-1">
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex justify-between items-center mb-6">
                                <h3 class="text-lg font-medium text-gray-900">Recent Activity</h3>
                                <span class="text-sm text-gray-500">Last 7 days</span>
                            </div>
                            <div class="space-y-4">
                                @forelse($recentActivities ?? [] as $activity)
                                    <div class="flex items-start space-x-3">
                                        <div class="flex-shrink-0">
                                            <span class="inline-flex items-center justify-center h-8 w-8 rounded-full {{
                                                $activity->type === 'lost' ? 'bg-red-100 text-red-600' :
                                                ($activity->type === 'found' ? 'bg-green-100 text-green-600' : 'bg-blue-100 text-blue-600')
                                            }}">
                                                <i class="fas {{
                                                    $activity->type === 'lost' ? 'fa-search' :
                                                    ($activity->type === 'found' ? 'fa-box' : 'fa-check')
                                                }}"></i>
                                            </span>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <p class="text-sm font-medium text-gray-900">{{ $activity->description }}</p>
                                            <div class="mt-1 flex items-center text-sm text-gray-500">
                                                <i class="fas fa-clock mr-1.5"></i>
                                                {{ $activity->created_at->diffForHumans() }}
                                            </div>
                                        </div>
                                        @if($activity->link)
                                            <a href="{{ $activity->link }}" class="flex-shrink-0 text-blue-600 hover:text-blue-800">
                                                <i class="fas fa-chevron-right"></i>
                                            </a>
                                        @endif
                                    </div>
                                @empty
                                    <div class="text-center py-4">
                                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 mb-4">
                                            <i class="fas fa-inbox text-gray-400 text-2xl"></i>
                                        </div>
                                        <p class="text-gray-500 text-sm">No recent activity</p>
                                        <a href="{{ route('products.report-item') }}" class="mt-2 inline-flex items-center text-sm font-medium text-blue-600 hover:text-blue-800">
                                            Report an item
                                            <i class="fas fa-arrow-right ml-1"></i>
                                        </a>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chat Interface -->
    @livewire('chat-interfaces')
</x-app-layout>
