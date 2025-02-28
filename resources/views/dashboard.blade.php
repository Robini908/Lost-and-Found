<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0">
            <div>
                <h2 class="text-2xl font-semibold text-gray-800">
                    {{ __('Welcome back') }}, {{ Auth::user()->name }}
                </h2>
                <p class="mt-1 text-sm text-gray-600">Here's an overview of your lost and found items</p>
            </div>
            <div class="flex flex-col sm:flex-row gap-3">
                <a href="{{ route('products.report-item') }}"
                   class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-sm text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 shadow-sm transition-all duration-200">
                    <i class="fas fa-exclamation-circle text-blue-500 mr-2"></i>
                    {{ __('Report Lost Item') }}
                </a>
                <a href="{{ route('products.report-found-item') }}"
                   class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-sm text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 shadow-sm transition-all duration-200">
                    <i class="fas fa-search text-green-500 mr-2"></i>
                    {{ __('Report Found Item') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Stats Section -->
            <div class="mb-6">
                <livewire:dashboard-stats />
            </div>

            <!-- Charts Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <!-- Distribution Chart -->
                <div>
                    <livewire:charts.items-distribution-chart />
                </div>

                <!-- Timeline Chart -->
                <div>
                    <livewire:charts.items-timeline-chart />
                </div>
            </div>

            <!-- Category Distribution Chart - Full Width -->
            <div class="mb-6">
                <livewire:charts.category-distribution-chart />
            </div>

            <!-- Main Content Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Quick Actions Section -->
                <div class="lg:col-span-2">
                    <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-100">
                        <div class="p-6">
                            <div class="flex justify-between items-center mb-6">
                                <h3 class="text-lg font-medium text-gray-900">Recent Items</h3>
                                <a href="{{ route('products.view-items') }}" class="text-sm text-blue-600 hover:text-blue-800 font-medium flex items-center">
                                    View All
                                    <i class="fas fa-arrow-right ml-2 text-xs"></i>
                                </a>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @forelse($recentItems ?? [] as $item)
                                    <div class="group bg-gray-50 rounded-lg p-4 hover:bg-white hover:shadow-md transition-all duration-200 border border-gray-100">
                                        <div class="flex items-center space-x-4">
                                            @if($item->images->isNotEmpty())
                                                <img src="{{ Storage::url($item->images->first()->image_path) }}"
                                                     alt="{{ $item->title }}"
                                                     class="w-16 h-16 rounded-lg object-cover">
                                            @else
                                                <div class="w-16 h-16 rounded-lg bg-gray-200 flex items-center justify-center">
                                                    <i class="fas fa-image text-gray-400 text-2xl"></i>
                                                </div>
                                            @endif
                                            <div class="flex-1 min-w-0">
                                                <h4 class="text-sm font-medium text-gray-900 group-hover:text-blue-600 transition-colors">
                                                    {{ $item->title }}
                                                </h4>
                                                <p class="mt-1 text-sm text-gray-500 truncate">{{ $item->description }}</p>
                                                <div class="mt-2 flex items-center">
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $item->item_type === 'lost' ? 'bg-red-50 text-red-700' : 'bg-green-50 text-green-700' }}">
                                                        {{ ucfirst($item->item_type) }}
                                                    </span>
                                                    <span class="mx-2 text-gray-300">•</span>
                                                    <span class="text-xs text-gray-500">
                                                        <i class="fas fa-clock mr-1"></i>
                                                        {{ $item->created_at->diffForHumans() }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="col-span-2 text-center py-12 bg-gray-50 rounded-lg">
                                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 mb-4">
                                            <i class="fas fa-box-open text-gray-400 text-2xl"></i>
                                        </div>
                                        <h3 class="text-sm font-medium text-gray-900">No items yet</h3>
                                        <p class="mt-2 text-sm text-gray-500">Start by reporting a lost or found item</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity Section -->
                <div class="lg:col-span-1">
                    <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-100">
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
                                                $activity->type === 'lost' ? 'bg-red-50 text-red-600' :
                                                ($activity->type === 'found' ? 'bg-green-50 text-green-600' : 'bg-blue-50 text-blue-600')
                                            }}">
                                                <i class="fas {{
                                                    $activity->type === 'lost' ? 'fa-search' :
                                                    ($activity->type === 'found' ? 'fa-box' : 'fa-check')
                                                }}"></i>
                                            </span>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <p class="text-sm text-gray-900">{{ $activity->description }}</p>
                                            <div class="mt-1 flex items-center text-xs text-gray-500">
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
                                    <div class="text-center py-8">
                                        <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-gray-100 mb-4">
                                            <i class="fas fa-clock text-gray-400"></i>
                                        </div>
                                        <p class="text-sm text-gray-500">No recent activity</p>
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
