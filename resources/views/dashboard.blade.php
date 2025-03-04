@php
    use App\Models\LostItem;
    $userHasItems = LostItem::where('user_id', auth()->id())->exists();
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Main Welcome Card -->
            <div class="bg-white rounded-3xl shadow-sm overflow-hidden mb-6">
                <!-- Top Section with Google-style gradient -->
                <div class="bg-white px-6 md:px-8 py-8">
                    <div class="flex flex-col md:flex-row justify-between items-center gap-6">
                        <div class="text-center md:text-left">
                            <h2 class="text-2xl font-normal text-gray-900 tracking-tight">
                                {{ __('Welcome back') }}, <span class="font-medium">{{ Auth::user()->name }}</span>
                            </h2>
                            <p class="mt-1 text-gray-600 text-sm">
                                @if(!$userHasItems)
                                    Let's help you get started with Lost & Found
                                @else
                                    Here's an overview of your lost and found items
                                @endif
                            </p>
                        </div>

                    </div>
                </div>
            </div>

            <!-- No Items Message Card (Google Material Style) -->
            @if(!$userHasItems)
                <div class="bg-white rounded-3xl shadow-sm overflow-hidden">
                    <div class="px-6 md:px-8 py-6">
                        <div class="flex items-start gap-6">
                            <!-- Icon Column -->
                            <div class="hidden md:flex flex-col items-center">
                                <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center">
                                    <i class="fas fa-clipboard-list text-blue-600 text-xl"></i>
                                </div>
                                <div class="h-full w-px bg-gray-200 my-4"></div>
                                <div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center">
                                    <i class="fas fa-users text-green-600 text-xl"></i>
                                </div>
                            </div>

                            <!-- Content Column -->
                            <div class="flex-1">
                                <h3 class="text-xl font-normal text-gray-900 mb-4">Ready to start?</h3>
                                <div class="space-y-4">
                                    <div class="bg-gray-50 rounded-2xl p-4">
                                        <h4 class="font-medium text-gray-900 mb-2">Start Your Lost & Found Journey</h4>
                                        <p class="text-gray-600 text-sm">You haven't reported any items yet. Take the first step in helping our community by reporting a lost item or registering a found item.</p>
                                    </div>
                                    <div class="bg-blue-50 rounded-2xl p-4">
                                        <h4 class="font-medium text-blue-900 mb-2">Join Our Community Effort</h4>
                                        <p class="text-blue-700 text-sm">Every reported item increases the chances of successful returns. Be part of our growing network of helpful community members.</p>
                                    </div>
                                    <div class="flex gap-4 mt-6">
                                        <a href="{{ route('products.report-item') }}"
                                           class="flex-1 inline-flex items-center justify-center px-6 py-2.5 bg-blue-600 text-white rounded-full font-medium text-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 shadow-sm transition-all duration-200">
                                            <i class="fas fa-plus-circle mr-2"></i>
                                            Report Your First Item
                                        </a>
                                        <a href="{{ route('products.view-items') }}"
                                           class="flex-1 inline-flex items-center justify-center px-6 py-2.5 border border-gray-300 text-gray-700 rounded-full font-medium text-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all duration-200">
                                            <i class="fas fa-search mr-2"></i>
                                            Browse Items
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Stats Section -->
            <div class="mb-8">
                <livewire:dashboard-stats />
            </div>

            <!-- Charts Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                <!-- Distribution Chart -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow duration-200">
                    <livewire:charts.items-distribution-chart />
                </div>

                <!-- Timeline Chart -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow duration-200">
                    <livewire:charts.items-timeline-chart />
                </div>
            </div>

            <!-- Category Distribution Chart - Full Width -->
            <div class="mb-8">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow duration-200">
                <livewire:charts.category-distribution-chart />
                </div>
            </div>

            <!-- Main Content Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Quick Actions Section -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow duration-200">
                        <div class="p-6">
                            <div class="flex justify-between items-center mb-8">
                                <div>
                                    <h3 class="text-2xl font-bold text-gray-900">Recent Items</h3>
                                    <p class="text-sm text-gray-500 mt-1">Latest lost and found items in the system</p>
                                </div>
                                <a href="{{ route('products.view-items') }}"
                                   class="inline-flex items-center px-5 py-2.5 bg-blue-600 text-white rounded-xl text-sm font-medium hover:bg-blue-700 transition-colors duration-200 shadow-sm">
                                    View All Items
                                    <i class="fas fa-arrow-right ml-2"></i>
                                </a>
                            </div>

                            @if($recentItems->isEmpty())
                                <div class="col-span-2">
                                    <div class="text-center py-16 bg-gray-50 rounded-xl border-2 border-dashed border-gray-200">
                                        <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-white shadow-sm mb-4">
                                            <i class="fas fa-box-open text-gray-400 text-3xl"></i>
                                        </div>
                                        <h3 class="text-xl font-semibold text-gray-900 mb-2">No items yet</h3>
                                        <p class="text-gray-500 mb-6">Start by reporting a lost or found item</p>
                                        <div class="flex justify-center gap-4">
                                            <a href="{{ route('products.report-item') }}"
                                               class="inline-flex items-center px-5 py-2.5 bg-blue-600 text-white rounded-xl text-sm font-medium hover:bg-blue-700 transition-colors duration-200 shadow-sm">
                                                <i class="fas fa-exclamation-circle mr-2"></i>
                                                Report Lost Item
                                            </a>
                                            <a href="{{ route('products.report-found-item') }}"
                                               class="inline-flex items-center px-5 py-2.5 bg-green-500 text-white rounded-xl text-sm font-medium hover:bg-green-600 transition-colors duration-200 shadow-sm">
                                                <i class="fas fa-search mr-2"></i>
                                                Report Found Item
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @else
                                @unless($userHasItems)
                                    <div class="mb-6 bg-blue-50 border border-blue-100 rounded-xl p-4">
                                        <div class="flex items-start">
                                            <div class="flex-shrink-0">
                                                <i class="fas fa-info-circle text-blue-500 text-xl"></i>
                                            </div>
                                            <div class="ml-3">
                                                <h3 class="text-sm font-medium text-blue-800">Viewing Community Items</h3>
                                                <div class="mt-1 text-sm text-blue-600">
                                                    <p>You haven't reported any items yet. These are items reported by other community members. Would you like to report a lost or found item?</p>
                                                    <div class="mt-3 flex gap-3">
                                                        <a href="{{ route('products.report-item') }}" class="inline-flex items-center text-sm font-medium text-blue-700 hover:text-blue-800">
                                                            <i class="fas fa-exclamation-circle mr-1.5"></i>
                                                            Report Lost Item
                                                        </a>
                                                        <a href="{{ route('products.report-found-item') }}" class="inline-flex items-center text-sm font-medium text-blue-700 hover:text-blue-800">
                                                            <i class="fas fa-search mr-1.5"></i>
                                                            Report Found Item
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endunless
                            @endif
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                @forelse($recentItems ?? [] as $item)
                                    <div class="group bg-white rounded-xl p-5 hover:shadow-lg transition-all duration-300 border border-gray-100 relative overflow-hidden">
                                        <!-- Status Badge -->
                                        <div class="absolute top-0 right-0 mt-4 mr-4">
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{
                                                $item->item_type === LostItem::TYPE_REPORTED ? 'bg-red-100 text-red-700' :
                                                ($item->item_type === LostItem::TYPE_FOUND ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700')
                                            }}">
                                                <i class="fas {{
                                                    $item->item_type === LostItem::TYPE_REPORTED ? 'fa-exclamation-circle' :
                                                    ($item->item_type === LostItem::TYPE_FOUND ? 'fa-check-circle' : 'fa-search')
                                                }} mr-1"></i>
                                                {{ ucfirst($item->item_type) }}
                                            </span>
                                        </div>

                                        <div class="flex flex-col sm:flex-row gap-6">
                                            <!-- Item Image -->
                                            <div class="flex-shrink-0">
                                                <a href="{{ route('lost-items.show', $item->hashed_id) }}" class="block">
                                            @if($item->images->isNotEmpty())
                                                <img src="{{ Storage::url($item->images->first()->image_path) }}"
                                                     alt="{{ $item->title }}"
                                                             class="w-full sm:w-32 h-32 rounded-xl object-cover shadow-sm group-hover:opacity-90 transition-opacity">
                                            @else
                                                        <div class="w-full sm:w-32 h-32 rounded-xl bg-gray-100 flex items-center justify-center group-hover:bg-gray-200 transition-colors">
                                                            <i class="fas fa-image text-gray-400 text-3xl"></i>
                                                </div>
                                            @endif
                                                </a>
                                            </div>

                                            <!-- Item Details -->
                                            <div class="flex-1 min-w-0">
                                                <a href="{{ route('lost-items.show', $item->hashed_id) }}" class="block group-hover:text-blue-600 transition-colors">
                                                    <h4 class="text-lg font-semibold text-gray-900 mb-2 pr-20">
                                                    {{ $item->title }}
                                                </h4>
                                                </a>
                                                <p class="text-sm text-gray-600 line-clamp-2 mb-4">{{ $item->description }}</p>

                                                <!-- Item Metadata -->
                                                <div class="flex flex-wrap items-center gap-4">
                                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700">
                                                        <i class="fas fa-folder mr-1.5 text-gray-500"></i>
                                                        {{ $item->category->name }}
                                                    </span>

                                                    @if($item->location)
                                                        <span class="inline-flex items-center text-xs text-gray-500">
                                                            <i class="fas fa-map-marker-alt mr-1.5"></i>
                                                            {{ $item->location }}
                                                        </span>
                                                    @endif

                                                    <span class="inline-flex items-center text-xs text-gray-500">
                                                        <i class="fas fa-clock mr-1.5"></i>
                                                        {{ $item->created_at->diffForHumans() }}
                                                    </span>

                                                    @if($item->is_verified)
                                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-700">
                                                            <i class="fas fa-check-circle mr-1"></i>
                                                            Verified
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="col-span-2">
                                        <div class="text-center py-16 bg-gray-50 rounded-xl border-2 border-dashed border-gray-200">
                                            <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-white shadow-sm mb-4">
                                                <i class="fas fa-box-open text-gray-400 text-3xl"></i>
                                            </div>
                                            <h3 class="text-xl font-semibold text-gray-900 mb-2">No items yet</h3>
                                            <p class="text-gray-500 mb-6">Start by reporting a lost or found item</p>
                                            <div class="flex justify-center gap-4">
                                                <a href="{{ route('products.report-item') }}"
                                                   class="inline-flex items-center px-5 py-2.5 bg-blue-600 text-white rounded-xl text-sm font-medium hover:bg-blue-700 transition-colors duration-200 shadow-sm">
                                                    <i class="fas fa-exclamation-circle mr-2"></i>
                                                    Report Lost Item
                                                </a>
                                                <a href="{{ route('products.report-found-item') }}"
                                                   class="inline-flex items-center px-5 py-2.5 bg-green-500 text-white rounded-xl text-sm font-medium hover:bg-green-600 transition-colors duration-200 shadow-sm">
                                                    <i class="fas fa-search mr-2"></i>
                                                    Report Found Item
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity Section -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow duration-200">
                        <div class="p-6">
                            <div class="flex justify-between items-center mb-6">
                                <h3 class="text-xl font-semibold text-gray-900">Recent Activity</h3>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-700">
                                    Last 7 days
                                </span>
                            </div>
                            <div class="space-y-6">
                                @forelse($recentActivities ?? [] as $activity)
                                    <div class="flex items-start space-x-4">
                                        <div class="flex-shrink-0">
                                            <span class="inline-flex items-center justify-center h-10 w-10 rounded-xl {{
                                                $activity->type === 'lost' ? 'bg-red-50 text-red-600' :
                                                ($activity->type === 'found' ? 'bg-green-50 text-green-600' : 'bg-blue-50 text-blue-600')
                                            }}">
                                                <i class="fas {{
                                                    $activity->type === 'lost' ? 'fa-search' :
                                                    ($activity->type === 'found' ? 'fa-box' : 'fa-check')
                                                }} text-lg"></i>
                                            </span>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <p class="text-sm text-gray-900 font-medium">{{ $activity->description }}</p>
                                            <div class="mt-2 flex items-center text-xs text-gray-500">
                                                <i class="fas fa-clock mr-1.5"></i>
                                                {{ $activity->created_at->diffForHumans() }}
                                            </div>
                                        </div>
                                        @if($activity->link)
                                            <a href="{{ $activity->link }}"
                                               class="flex-shrink-0 inline-flex items-center justify-center h-8 w-8 rounded-lg bg-gray-50 hover:bg-gray-100 text-gray-400 hover:text-gray-500 transition-colors duration-200">
                                                <i class="fas fa-chevron-right"></i>
                                            </a>
                                        @endif
                                    </div>
                                @empty
                                    <div class="text-center py-8">
                                        <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-gray-100 mb-4">
                                            <i class="fas fa-clock text-gray-400 text-xl"></i>
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
