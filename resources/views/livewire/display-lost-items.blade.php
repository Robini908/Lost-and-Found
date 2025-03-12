<div class="min-h-screen bg-gray-50 py-8">
    <!-- Floating Action Bar for Bulk Actions -->
    @if($canDelete && $totalSelected > 0)
        <div class="fixed bottom-0 inset-x-0 pb-6 z-50"
             x-data="{ show: true }"
             x-show="show"
             x-transition:enter="transform transition ease-in-out duration-500 sm:duration-700"
             x-transition:enter-start="translate-y-full"
             x-transition:enter-end="translate-y-0"
             x-transition:leave="transform transition ease-in-out duration-500 sm:duration-700"
             x-transition:leave-start="translate-y-0"
             x-transition:leave-end="translate-y-full">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-white/80 backdrop-blur-lg rounded-2xl shadow-lg border border-gray-200/80 px-6 py-4">
                    <div class="flex items-center justify-between flex-wrap gap-4">
                        <div class="flex items-center gap-4">
                            <div class="flex items-center gap-2">
                                <span class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-blue-100 text-blue-600">
                                    <i class="fas fa-check-square text-lg"></i>
                                </span>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $totalSelected }} items selected</p>
                                    <p class="text-xs text-gray-500">from {{ $items->total() }} total items</p>
                                </div>
                            </div>
                            <div class="h-8 w-px bg-gray-200"></div>
                            <div class="flex items-center gap-3">
                                <button wire:click="selectAll"
                                    class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200">
                                    <i class="fas fa-check-double mr-2 text-blue-500"></i>
                                    Select All
                                </button>
                                <button wire:click="deselectAll"
                                    class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200">
                                    <i class="fas fa-times mr-2 text-gray-500"></i>
                                    Deselect All
                                </button>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <button wire:click="deleteSelected"
                                wire:confirm="Are you sure you want to delete {{ $totalSelected }} selected items? This action cannot be undone."
                                class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 active:bg-red-800 text-white text-sm font-medium rounded-lg transition-all duration-200 transform hover:-translate-y-0.5">
                                <i class="fas fa-trash-alt mr-2"></i>
                                Delete Selected
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Lost & Found Items</h1>
                <p class="mt-2 text-sm text-gray-600">Browse through reported items or search for specific ones</p>
            </div>

            <div class="mt-4 md:mt-0 flex items-center space-x-3">
            <!-- View Toggle Buttons -->
                <div class="flex items-center space-x-2">
                <button wire:click="toggleView('grid')"
                    class="inline-flex items-center px-4 py-2 rounded-md {{ $view === 'grid' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50' }} border border-gray-300 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <i class="fas fa-th-large mr-2"></i>
                    Grid
                </button>
                <button wire:click="toggleView('list')"
                    class="inline-flex items-center px-4 py-2 rounded-md {{ $view === 'list' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50' }} border border-gray-300 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <i class="fas fa-list mr-2"></i>
                    List
                </button>
                <button wire:click="toggleView('map')"
                    class="inline-flex items-center px-4 py-2 rounded-md {{ $view === 'map' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50' }} border border-gray-300 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <i class="fas fa-map-marker-alt mr-2"></i>
                    Map
                </button>
                </div>
            </div>
        </div>

        <!-- Search and Filters -->
        <div class="mb-8">
            <div class="flex flex-col md:flex-row md:items-center md:space-x-4">
                <!-- Search -->
                <div class="flex-1 min-w-0">
                    <div class="relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                        <input type="text" wire:model.live.debounce.300ms="search"
                            class="block w-full pl-10 pr-12 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                            placeholder="Search for items...">
                    </div>
                </div>

                <!-- Status Filter -->
                <div class="mt-4 md:mt-0">
                    <select wire:model.live="status"
                        class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                        <option value="">All Status</option>
                        <option value="lost">Lost</option>
                        <option value="found">Found</option>
                        <option value="claimed">Claimed</option>
                        <option value="returned">Returned</option>
                    </select>
                </div>

                <!-- Category Filter -->
                <div class="mt-4 md:mt-0">
                    <select wire:model.live="category"
                        class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Advanced Filters Toggle -->
                <div class="mt-4 md:mt-0">
                    <button wire:click="toggleAdvancedFilters"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fas fa-filter mr-2"></i>
                        Filters
                        @if($showAdvancedFilters)
                            <i class="fas fa-chevron-up ml-2"></i>
                        @else
                            <i class="fas fa-chevron-down ml-2"></i>
                        @endif
                    </button>
                </div>
            </div>

            <!-- Advanced Filters Panel -->
            <div class="{{ $showAdvancedFilters ? 'block' : 'hidden' }} relative mt-4">
                <div class="bg-white rounded-lg shadow-lg border border-gray-200 p-4 transform transition-all duration-200 ease-in-out">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- Condition -->
                        <div>
                            <label for="condition" class="block text-sm font-medium text-gray-700">Condition</label>
                            <select wire:model.live="condition" id="condition"
                                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                <option value="">Any Condition</option>
                                <option value="new">New</option>
                                <option value="like_new">Like New</option>
                                <option value="good">Good</option>
                                <option value="fair">Fair</option>
                                <option value="poor">Poor</option>
                            </select>
                        </div>

                        <!-- Brand -->
                        <div>
                            <label for="brand" class="block text-sm font-medium text-gray-700">Brand</label>
                            <input type="text" wire:model.live.debounce.300ms="brand" id="brand"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                placeholder="Enter brand name">
                        </div>

                        <!-- Color -->
                        <div>
                            <label for="color" class="block text-sm font-medium text-gray-700">Color</label>
                            <input type="text" wire:model.live.debounce.300ms="color" id="color"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                placeholder="Enter color">
                        </div>

                        <!-- Location -->
                        <div>
                            <label for="location" class="block text-sm font-medium text-gray-700">Location</label>
                            <input type="text" wire:model.live.debounce.500ms="location" id="location"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                placeholder="Enter location">
                        </div>

                        <!-- Radius -->
                        <div>
                            <label for="radius" class="block text-sm font-medium text-gray-700">Radius (km)</label>
                            <input type="number" wire:model.live.debounce.300ms="radius" id="radius"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                placeholder="Search radius">
                        </div>

                        <!-- Date Range -->
                        <div>
                            <label for="dateRange" class="block text-sm font-medium text-gray-700">Date Range</label>
                            <input type="text" wire:model.live="dateRange" id="dateRange"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                placeholder="Select date range">
                        </div>
                    </div>

                    <!-- Reset Filters -->
                    <div class="mt-4 flex justify-end">
                        <button wire:click="resetFilters"
                            class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <i class="fas fa-undo mr-2"></i>
                            Reset Filters
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content Area -->
        <div class="bg-white rounded-lg shadow">
            @if($items->isEmpty())
                <div class="flex flex-col items-center justify-center min-h-[400px] p-8 bg-white/50 backdrop-blur-sm rounded-xl shadow-sm border border-gray-100">
                    <!-- Animated Icon -->
                    <div class="mb-6 text-gray-400 animate-bounce">
                        <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>

                    <!-- Title with gradient text -->
                    <h3 class="text-2xl font-semibold mb-3 bg-gradient-to-r from-primary-600 to-secondary-600 bg-clip-text text-transparent">
                        No Items Found
                    </h3>

                    <!-- Context-aware message -->
                    <p class="text-gray-600 text-center max-w-md mb-6">
                        @if($this->hasActiveFilters())
                            It seems there are no items matching your current filters. Try adjusting your search criteria to find what you're looking for.
                        @else
                            There are currently no lost items in the system. Check back later or be the first to report a lost item.
                        @endif
                    </p>

                    <!-- Action buttons -->
                    <div class="flex gap-4">
                        @if($this->hasActiveFilters())
                            <button wire:click="resetFilters" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-sm text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition-all duration-200">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                Clear Filters
                            </button>
                        @endif

                        <a href="{{ route('products.report-item') }}" class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white font-semibold text-sm rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition-all duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Report Lost Item
                        </a>
                    </div>
                </div>
            @else
            <!-- Map View -->
            @if($view === 'map')
                <div class="h-[600px] relative rounded-lg overflow-hidden" wire:ignore>
                    <div id="map" class="w-full h-full"></div>
                </div>

                <script>
                    document.addEventListener('livewire:initialized', function () {
                        const map = new google.maps.Map(document.getElementById('map'), {
                            center: { lat: @json($mapCenter['lat']), lng: @json($mapCenter['lng']) },
                            zoom: @json($mapZoom),
                            styles: [
                                {
                                    featureType: "poi",
                                    elementType: "labels",
                                    stylers: [{ visibility: "off" }]
                                }
                            ]
                        });

                        const markers = @json($markers);
                        const bounds = new google.maps.LatLngBounds();
                        const infoWindow = new google.maps.InfoWindow();

                        markers.forEach(marker => {
                            if (marker.lat && marker.lng) {
                                const position = new google.maps.LatLng(marker.lat, marker.lng);
                                bounds.extend(position);

                                const mapMarker = new google.maps.Marker({
                                    position: position,
                                    map: map,
                                    title: marker.title,
                                    icon: {
                                        url: marker.status === 'found' ? '/images/marker-found.png' : '/images/marker-lost.png',
                                        scaledSize: new google.maps.Size(32, 32)
                                    }
                                });

                                mapMarker.addListener('click', () => {
                                    const content = `
                                        <div class="p-2">
                                            <h3 class="font-semibold">${marker.title}</h3>
                                            ${marker.image ? `<img src="${marker.image}" class="w-32 h-32 object-cover mt-2">` : ''}
                                            <button onclick="Livewire.dispatch('viewDetails', { itemId: ${marker.id} })"
                                                class="mt-2 px-3 py-1 bg-blue-600 text-white rounded-md text-sm">
                                                View Details
                                            </button>
                                        </div>
                                    `;
                                    infoWindow.setContent(content);
                                    infoWindow.open(map, mapMarker);
                                });
                            }
                        });

                        if (!bounds.isEmpty()) {
                            map.fitBounds(bounds);
                        }
                    });
                </script>
            @elseif($view === 'list')
                <!-- List View -->
                <div class="divide-y divide-gray-200">
                    @foreach($items as $item)
                            <div class="group hover:bg-gray-50/50 transition-all duration-300">
                                <div class="p-6">
                                    <div class="flex items-start gap-6">
                                        <!-- Selection & Image Column -->
                                        <div class="flex-shrink-0 flex flex-col items-center gap-4">
                                            @if($canDelete)
                                                <label class="inline-flex items-center">
                                                    <input type="checkbox"
                                                           wire:model.live="selectedItems"
                                                           value="{{ $item->id }}"
                                                           class="form-checkbox h-5 w-5 text-blue-600 rounded border-gray-300 focus:ring-blue-500 transition-colors duration-200">
                                                    <span class="sr-only">Select item</span>
                                                </label>
                                            @endif

                                            <!-- Image Container with Aspect Ratio -->
                                            <div class="relative w-40 aspect-[4/3] bg-gray-100 rounded-xl overflow-hidden shadow-sm border border-gray-200/80">
                                    @if($item->images->first())
                                        <img src="{{ asset('storage/' . $item->images->first()->image_path) }}"
                                             alt="{{ $item->title }}"
                                             class="absolute inset-0 w-full h-full object-cover">
                                                    @if($item->images->count() > 1)
                                                        <div class="absolute top-2 right-2">
                                                            <span class="px-2 py-1 text-xs font-medium text-white bg-black/50 rounded-full backdrop-blur-sm">
                                                                <i class="fas fa-images mr-1"></i>
                                                                {{ $item->images->count() }}
                                                            </span>
                                                        </div>
                                                    @endif
                                    @else
                                                    <div class="absolute inset-0 flex flex-col items-center justify-center">
                                                        <i class="fas fa-image text-gray-400 text-3xl mb-2"></i>
                                                        <span class="text-xs text-gray-400">No image</span>
                                        </div>
                                    @endif
                                </div>
                                        </div>

                                        <!-- Content Column -->
                                <div class="flex-1 min-w-0">
                                            <div class="flex items-start justify-between gap-4">
                                                <div class="flex-1">
                                                    <!-- Title and Status -->
                                                    <div class="flex items-center gap-3 mb-2">
                                                        <h3 class="text-lg font-semibold text-gray-900 group-hover:text-blue-600 transition-colors duration-200">
                                                            {{ $item->title }}
                                                        </h3>
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                            {{ $item->status === 'found' ? 'bg-green-100 text-green-800' : '' }}
                                                            {{ $item->status === 'lost' ? 'bg-red-100 text-red-800' : '' }}
                                                            {{ $item->status === 'claimed' ? 'bg-blue-100 text-blue-800' : '' }}">
                                                            <i class="fas fa-circle text-[8px] mr-1.5"></i>
                                            {{ ucfirst($item->status) }}
                                        </span>
                                                    </div>

                                                    <!-- Description -->
                                                    <p class="text-sm text-gray-600 line-clamp-2 mb-4">{{ $item->description }}</p>

                                                    <!-- Item Details -->
                                                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                                                        <!-- Category -->
                                                        <div class="flex items-center text-sm text-gray-600">
                                                            <i class="fas fa-folder text-blue-500 mr-2"></i>
                                                            <span class="truncate">{{ $item->category->name }}</span>
                                                        </div>
                                                        <!-- Location -->
                                                        <div class="flex items-center text-sm text-gray-600">
                                                            <i class="fas fa-map-marker-alt text-red-500 mr-2"></i>
                                                            <span class="truncate">{{ $item->location_address ?: $item->area ?: 'Location not specified' }}</span>
                                                        </div>
                                                        <!-- Date -->
                                                        <div class="flex items-center text-sm text-gray-600">
                                                            <i class="fas fa-calendar-alt text-purple-500 mr-2"></i>
                                                            <span>{{ $item->created_at->format('M d, Y') }}</span>
                                                        </div>
                                                        <!-- Time -->
                                                        <div class="flex items-center text-sm text-gray-600">
                                                            <i class="fas fa-clock text-amber-500 mr-2"></i>
                                                            <span>{{ $item->created_at->format('h:i A') }}</span>
                                                        </div>
                                                    </div>
                                    </div>

                                                <!-- Actions Column -->
                                                <div class="flex flex-col items-end gap-3">
                                                    @if($canDelete)
                                                        <button
                                                            wire:click="deleteItem({{ $item->id }})"
                                                            wire:confirm="Are you sure you want to delete this item?"
                                                            class="inline-flex items-center p-2 text-red-600 hover:text-red-800 hover:bg-red-50 rounded-full transition-colors duration-200">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </button>
                                                    @endif

                                        <button wire:click="viewDetails({{ $item->id }})"
                                                            class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 hover:border-blue-500 hover:text-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 group">
                                                        <i class="fas fa-eye mr-2 text-gray-400 group-hover:text-blue-500"></i>
                                            View Details
                                        </button>
                                                </div>
                                            </div>

                                            <!-- Additional Details -->
                                            @if($item->brand || $item->color || $item->condition)
                                                <div class="mt-4 pt-4 border-t border-gray-100">
                                                    <div class="flex items-center gap-4 text-sm text-gray-600">
                                                        @if($item->brand)
                                                            <span class="inline-flex items-center">
                                                                <i class="fas fa-tag text-gray-400 mr-1.5"></i>
                                                                {{ $item->brand }}
                                                            </span>
                                                        @endif
                                                        @if($item->color)
                                                            <span class="inline-flex items-center">
                                                                <i class="fas fa-palette text-gray-400 mr-1.5"></i>
                                                                {{ $item->color }}
                                                            </span>
                                                        @endif
                                                        @if($item->condition)
                                                            <span class="inline-flex items-center">
                                                                <i class="fas fa-star text-gray-400 mr-1.5"></i>
                                                                {{ ucfirst($item->condition) }} Condition
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                        @endif
                                        </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <!-- Grid View -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 p-6">
                    @foreach($items as $item)
                        <div class="group relative bg-white rounded-xl shadow-sm hover:shadow-lg transition-all duration-300 border border-gray-100 overflow-hidden">
                            <!-- Admin Controls Bar - Fixed at top -->
                            @if($canDelete)
                                <div class="absolute top-0 inset-x-0 z-20 p-3 bg-gradient-to-b from-black/50 to-transparent">
                                    <div class="flex items-center justify-between">
                                        <label class="inline-flex items-center">
                                            <input type="checkbox"
                                                   wire:model.live="selectedItems"
                                                   value="{{ $item->id }}"
                                                   class="form-checkbox h-5 w-5 text-blue-600 rounded border-gray-300 focus:ring-blue-500 transition-colors duration-200">
                                            <span class="sr-only">Select item</span>
                                        </label>
                                        <button
                                            wire:click="deleteItem({{ $item->id }})"
                                            wire:confirm="Are you sure you want to delete this item?"
                                            class="inline-flex items-center p-2 bg-red-100 text-red-600 rounded-full hover:bg-red-200 transition-colors duration-200">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>
                                </div>
                            @endif

                            <!-- Main Content Area - Clickable for View Details -->
                            <button wire:click="viewDetails({{ $item->id }})" class="w-full text-left">
                        <!-- Image Container -->
                        <div class="relative aspect-[4/3] bg-gray-100 overflow-hidden">
                            <div class="absolute inset-0 flex items-center justify-center">
                                @if($item->images->first())
                                    <img src="{{ asset('storage/' . $item->images->first()->image_path) }}"
                                         alt="{{ $item->title }}"
                                         class="w-full h-full object-contain">
                                    @if($item->images->count() > 1)
                                        <div class="absolute top-3 right-3">
                                            <span class="px-2.5 py-1.5 text-xs font-medium text-white bg-black/50 rounded-full backdrop-blur-sm">
                                                <i class="fas fa-images mr-1.5"></i>
                                                {{ $item->images->count() }} photos
                                            </span>
                                        </div>
                                    @endif
                                @else
                                    <div class="text-center">
                                        <i class="fas fa-image text-gray-400 text-4xl mb-2"></i>
                                        <p class="text-sm text-gray-400">No image available</p>
                                    </div>
                                @endif
                            </div>
                            <!-- Hover Overlay -->
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-all duration-300"></div>
                        </div>

                        <!-- Content -->
                        <div class="p-5">
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex-1">
                                    <h3 class="text-lg font-semibold text-gray-900 mb-1.5 line-clamp-1 group-hover:text-blue-600 transition-colors duration-200">
                                        {{ $item->title }}
                                    </h3>
                                    <div class="flex items-center space-x-2">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            {{ $item->status === 'found' ? 'bg-green-100 text-green-800' : '' }}
                                            {{ $item->status === 'lost' ? 'bg-red-100 text-red-800' : '' }}
                                            {{ $item->status === 'claimed' ? 'bg-blue-100 text-blue-800' : '' }}">
                                            <i class="fas fa-circle text-[8px] mr-1.5"></i>
                                            {{ ucfirst($item->status) }}
                                        </span>
                                        @if($item->category)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                <i class="fas fa-folder text-[8px] mr-1.5"></i>
                                                {{ $item->category->name }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <p class="text-sm text-gray-600 line-clamp-2 mb-4">
                                {{ $item->description }}
                            </p>

                            <div class="flex items-center justify-between text-sm border-t border-gray-100 pt-4 mt-4">
                                <div class="flex items-center text-gray-500">
                                    <i class="fas fa-calendar-alt mr-1.5 text-gray-400"></i>
                                    {{ $item->created_at->diffForHumans() }}
                                </div>
                                <div class="flex items-center text-gray-500">
                                    <i class="fas fa-map-marker-alt mr-1.5 text-gray-400"></i>
                                    {{ Str::limit($item->location_type === 'map' ? $item->location_address : $item->area, 20) }}
                                </div>
                            </div>
                        </div>

                                <!-- View Details Indicator -->
                                <div class="absolute bottom-4 right-4 bg-blue-600 text-white rounded-full w-10 h-10 flex items-center justify-center transform translate-y-12 opacity-0 group-hover:translate-y-0 group-hover:opacity-100 transition-all duration-300">
                                    <i class="fas fa-eye"></i>
                                </div>
                            </button>

                            <!-- Selection Indicator -->
                            @if(in_array($item->id, $selectedItems))
                                <div class="absolute inset-0 bg-blue-50/50 pointer-events-none">
                                    <div class="absolute top-2 right-2">
                                        <span class="flex h-6 w-6 items-center justify-center rounded-full bg-blue-500 text-white">
                                            <i class="fas fa-check text-sm"></i>
                                        </span>
                                    </div>
                                </div>
                            @endif
                    </div>
                    @endforeach
                </div>
            @endif

            <!-- Pagination -->
            @if($items->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $items->links() }}
                </div>
                @endif
            @endif
        </div>
    </div>

    <!-- Item Details Modal -->
    <x-item-details-modal wire:model.live="showModal">
        @if($selectedItem)
            <x-slot:gallery>
                <div class="h-full flex items-center justify-center">
                    @if($selectedItem->images->count() > 0)
                    <div class="relative w-full h-full flex items-center justify-center">
                        @foreach($selectedItem->images as $index => $image)
                        <div x-show="activeImage === {{ $index }}"
                             x-transition:enter="transition ease-out duration-300"
                             x-transition:enter-start="opacity-0 transform scale-95"
                             x-transition:enter-end="opacity-100 transform scale-100"
                             x-transition:leave="transition ease-in duration-200"
                             x-transition:leave-start="opacity-100 transform scale-100"
                             x-transition:leave-end="opacity-0 transform scale-95"
                             class="absolute inset-0 flex items-center justify-center p-4">
                            <img src="{{ asset('storage/' . $image->image_path) }}"
                                 alt="Item image {{ $index + 1 }}"
                                 class="gallery-image max-w-full max-h-full object-contain rounded-lg shadow-2xl"
                                 @click="isFullscreen = true">
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center p-8">
                        <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-gray-800 mb-4">
                            <i class="fas fa-image text-gray-400 text-3xl"></i>
                        </div>
                        <p class="text-gray-400 text-sm">No images available</p>
                    </div>
                    @endif
                </div>
            </x-slot:gallery>

            <x-slot:header>
                <div class="relative">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center space-x-3">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                @if($selectedItem->status === 'found') bg-green-100 text-green-800
                                @elseif($selectedItem->status === 'claimed') bg-blue-100 text-blue-800
                                @else bg-yellow-100 text-yellow-800 @endif">
                                <i class="fas fa-circle text-[8px] mr-1.5"></i>
                                {{ ucfirst($selectedItem->status) }}
                            </span>
                            @if($selectedItem->category)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                                <i class="fas fa-folder text-[8px] mr-1.5"></i>
                                {{ $selectedItem->category->name }}
                            </span>
                            @endif
                        </div>
                        <button wire:click="closeModal" class="text-gray-400 hover:text-gray-500 transition-colors duration-200">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900">{{ $selectedItem->title }}</h2>
                    <div class="flex items-center mt-2 text-sm text-gray-500">
                        <i class="fas fa-clock mr-1.5"></i>
                        {{ $selectedItem->created_at->diffForHumans() }}
                    </div>
                </div>
            </x-slot:header>

            <x-slot:content>
                <div class="space-y-8">
                    <!-- Description -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-3 flex items-center">
                            <i class="fas fa-align-left text-blue-600 mr-2"></i>
                            Description
                        </h3>
                        <p class="text-gray-600 leading-relaxed">{{ $selectedItem->description }}</p>
                    </div>

                    <!-- Basic Info -->
                    <div class="bg-gray-50 rounded-xl p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                            Item Details
                        </h3>
                        <div class="grid grid-cols-2 gap-6">
                            @if($selectedItem->brand)
                            <div>
                                <dt class="text-sm font-medium text-gray-500 mb-1">Brand</dt>
                                <dd class="text-sm text-gray-900 bg-white rounded-lg p-3 border border-gray-100">
                                    {{ $selectedItem->brand }}
                                </dd>
                            </div>
                            @endif
                            @if($selectedItem->color)
                            <div>
                                <dt class="text-sm font-medium text-gray-500 mb-1">Color</dt>
                                <dd class="text-sm text-gray-900 bg-white rounded-lg p-3 border border-gray-100">
                                    {{ $selectedItem->color }}
                                </dd>
                            </div>
                            @endif
                            @if($selectedItem->condition)
                            <div>
                                <dt class="text-sm font-medium text-gray-500 mb-1">Condition</dt>
                                <dd class="text-sm text-gray-900 bg-white rounded-lg p-3 border border-gray-100">
                                    {{ ucfirst($selectedItem->condition) }}
                                </dd>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Location Info -->
                    @if($selectedItem->location_address || $selectedItem->area)
                    <div class="bg-gray-50 rounded-xl p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <i class="fas fa-map-marker-alt text-blue-600 mr-2"></i>
                            Location Information
                        </h3>
                        <div class="space-y-4">
                            @if($selectedItem->location_address)
                            <div class="bg-white rounded-lg p-4 border border-gray-100">
                                <p class="text-sm text-gray-900">{{ $selectedItem->location_address }}</p>
                            </div>
                            @endif
                            @if($selectedItem->area)
                            <div class="flex items-start space-x-2">
                                <i class="fas fa-map text-gray-400 mt-0.5"></i>
                                <p class="text-sm text-gray-600">Area: {{ $selectedItem->area }}</p>
                            </div>
                            @endif
                            @if($selectedItem->landmarks)
                            <div class="flex items-start space-x-2">
                                <i class="fas fa-landmark text-gray-400 mt-0.5"></i>
                                <p class="text-sm text-gray-600">Landmarks: {{ $selectedItem->landmarks }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
            </x-slot:content>

            <x-slot:footer>
                <div class="flex justify-between items-center">
                    <div class="flex items-center space-x-2 text-sm text-gray-500">
                        <i class="fas fa-user"></i>
                        <span>Posted by {{ $selectedItem->user->name }}</span>
                    </div>
                    <div class="flex space-x-3">
                        @if($selectedItem->status === 'lost')
                        <button wire:click="reportMatch({{ $selectedItem->id }})"
                            class="inline-flex items-center px-4 py-2 border border-transparent rounded-xl text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 shadow-sm transition-all duration-200">
                            <i class="fas fa-handshake mr-2"></i>
                            Report Match
                        </button>
                        @endif
                        <button wire:click="closeModal"
                            class="inline-flex items-center px-4 py-2 border border-gray-200 rounded-xl text-sm font-semibold text-gray-600 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 shadow-sm transition-all duration-200">
                            <i class="fas fa-times mr-2"></i>
                            Close
                        </button>
                    </div>
                </div>
            </x-slot:footer>
        @endif
    </x-item-details-modal>

    @push('scripts')
    <script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_api_key') }}&libraries=places"></script>
    @endpush
</div>
