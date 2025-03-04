<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Lost & Found Items</h1>
                <p class="mt-2 text-sm text-gray-600">Browse through reported items or search for specific ones</p>
            </div>

            <!-- View Toggle Buttons -->
            <div class="mt-4 md:mt-0 flex space-x-2">
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
                        <div class="p-4 hover:bg-gray-50 transition-colors duration-200">
                            <div class="flex items-start space-x-4">
                                <!-- Fixed size image container -->
                                <div class="relative flex-shrink-0 w-32 h-32 bg-gray-100 rounded-lg overflow-hidden">
                                    @if($item->images->first())
                                        <img src="{{ asset('storage/' . $item->images->first()->image_path) }}"
                                             alt="{{ $item->title }}"
                                             class="absolute inset-0 w-full h-full object-cover">
                                    @else
                                        <div class="flex items-center justify-center h-full">
                                            <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                                <!-- Item Details -->
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between">
                                        <span class="px-2.5 py-0.5 text-xs font-medium rounded-full
                                            @if($item->status === 'found') bg-green-100 text-green-800
                                            @elseif($item->status === 'claimed') bg-blue-100 text-blue-800
                                            @else bg-yellow-100 text-yellow-800 @endif">
                                            {{ ucfirst($item->status) }}
                                        </span>
                                        <span class="text-sm text-gray-500">{{ $item->created_at->diffForHumans() }}</span>
                                    </div>
                                    <h3 class="mt-1 text-lg font-semibold text-gray-900">{{ $item->title }}</h3>
                                    <p class="mt-1 text-sm text-gray-600">{{ $item->description }}</p>
                                    <div class="mt-2 flex items-center justify-between">
                                        <button wire:click="viewDetails({{ $item->id }})"
                                                class="inline-flex items-center px-3 py-1.5 border border-transparent text-sm font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                                            View Details
                                        </button>
                                        @if($item->category)
                                            <span class="text-sm text-gray-500">{{ $item->category->name }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <!-- Grid View -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 p-4">
                    @foreach($items as $item)
                    <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow duration-200 border border-gray-100 overflow-hidden">
                        <!-- Fixed height image container -->
                        <div class="relative w-full h-48 bg-gray-100">
                            @if($item->images->first())
                            <img src="{{ asset('storage/' . $item->images->first()->image_path) }}"
                                 alt="{{ $item->title }}"
                                 class="absolute inset-0 w-full h-full object-cover">
                            @else
                            <div class="flex items-center justify-center h-full bg-gray-50">
                                <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            @endif
                        </div>
                        <!-- Item Details -->
                        <div class="p-4">
                            <div class="flex items-center justify-between mb-2">
                                <span class="px-2.5 py-0.5 text-xs font-medium rounded-full
                                    @if($item->status === 'found') bg-green-100 text-green-800
                                    @elseif($item->status === 'claimed') bg-blue-100 text-blue-800
                                    @else bg-yellow-100 text-yellow-800 @endif">
                                    {{ ucfirst($item->status) }}
                                </span>
                                <span class="text-sm text-gray-500">{{ $item->created_at->diffForHumans() }}</span>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-1">{{ $item->title }}</h3>
                            <p class="text-sm text-gray-600 line-clamp-2 mb-3">{{ $item->description }}</p>
                            <div class="flex items-center justify-between">
                                <button wire:click="viewDetails({{ $item->id }})"
                                        class="inline-flex items-center px-3 py-1.5 border border-transparent text-sm font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                                    View Details
                                </button>
                                @if($item->category)
                                <span class="text-sm text-gray-500">{{ $item->category->name }}</span>
                                @endif
                            </div>
                        </div>
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
        </div>
    </div>

    <!-- Item Details Modal -->
    <x-item-details-modal wire:model.live="showModal">
        @if($selectedItem)
            <x-slot:gallery>
                <div class="h-full flex items-center justify-center p-4">
                    @if($selectedItem->images->count() > 0)
                    <div class="grid grid-cols-2 gap-2 h-full w-full">
                        @foreach($selectedItem->images as $index => $image)
                        <div class="relative group {{ $index === 0 ? 'col-span-2 row-span-2' : '' }} rounded-lg overflow-hidden">
                            <img src="{{ asset('storage/' . $image->image_path) }}"
                                 alt="Item image {{ $index + 1 }}"
                                 class="w-full h-full object-cover">
                            <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-30 transition-opacity duration-200"></div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="flex items-center justify-center h-full w-full bg-gray-800">
                        <svg class="w-24 h-24 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    @endif
                </div>
            </x-slot:gallery>

            <x-slot:header>
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <span class="px-3 py-1 text-sm font-medium rounded-full
                            @if($selectedItem->status === 'found') bg-green-100 text-green-800
                            @elseif($selectedItem->status === 'claimed') bg-blue-100 text-blue-800
                            @else bg-yellow-100 text-yellow-800 @endif">
                            {{ ucfirst($selectedItem->status) }}
                        </span>
                        @if($selectedItem->category)
                        <span class="text-sm text-gray-600">{{ $selectedItem->category->name }}</span>
                        @endif
                    </div>
                    <button wire:click="closeModal" class="text-gray-400 hover:text-gray-500">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <h2 class="text-2xl font-bold text-gray-900 mt-4">{{ $selectedItem->title }}</h2>
            </x-slot:header>

            <x-slot:content>
                <div class="space-y-6">
                    <!-- Basic Info -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">Basic Information</h3>
                        <dl class="grid grid-cols-2 gap-4">
                            @if($selectedItem->brand)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Brand</dt>
                                <dd class="text-sm text-gray-900">{{ $selectedItem->brand }}</dd>
                            </div>
                            @endif
                            @if($selectedItem->color)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Color</dt>
                                <dd class="text-sm text-gray-900">{{ $selectedItem->color }}</dd>
                            </div>
                            @endif
                            @if($selectedItem->condition)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Condition</dt>
                                <dd class="text-sm text-gray-900">{{ ucfirst($selectedItem->condition) }}</dd>
                            </div>
                            @endif
                        </dl>
                    </div>

                    <!-- Description -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Description</h3>
                        <p class="text-gray-600">{{ $selectedItem->description }}</p>
                    </div>

                    <!-- Location Info -->
                    @if($selectedItem->location_address || $selectedItem->area)
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">Location Information</h3>
                        @if($selectedItem->location_address)
                        <p class="text-sm text-gray-600">{{ $selectedItem->location_address }}</p>
                        @endif
                        @if($selectedItem->area)
                        <p class="text-sm text-gray-600 mt-2">Area: {{ $selectedItem->area }}</p>
                        @endif
                        @if($selectedItem->landmarks)
                        <p class="text-sm text-gray-600 mt-2">Landmarks: {{ $selectedItem->landmarks }}</p>
                        @endif
                    </div>
                    @endif
                </div>
            </x-slot:content>

            <x-slot:footer>
                <div class="flex justify-end space-x-3">
                    @if($selectedItem->status === 'lost')
                    <button wire:click="reportMatch({{ $selectedItem->id }})"
                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-xl text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 shadow-sm transition-all duration-200">
                        Report Match
                    </button>
                    @endif
                    <button wire:click="closeModal"
                        class="inline-flex items-center px-4 py-2 border border-gray-200 rounded-xl text-sm font-semibold text-gray-600 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 shadow-sm transition-all duration-200">
                        Close
                    </button>
                </div>
            </x-slot:footer>
        @endif
    </x-item-details-modal>

    @push('scripts')
    <script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_api_key') }}&libraries=places"></script>
    @endpush
</div>
