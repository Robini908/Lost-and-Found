<!-- Google Material Design UI -->
<div class="min-h-screen bg-gray-50">
    <!-- Top App Bar -->
    <div class="bg-white shadow-sm  top-0 ">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center space-x-4">
                    <h1 class="text-xl font-medium text-gray-800">
                        <i class="fas fa-search-location text-blue-600 mr-2"></i>
                        Item Matching
                    </h1>
                    @if(!$showItemMatchingPage)
                        <div class="hidden md:flex items-center bg-gray-50 rounded-full px-4 py-1.5">
                            <i class="fas fa-clock text-gray-400 mr-2"></i>
                            <span class="text-sm text-gray-600">Last updated: {{ now()->diffForHumans() }}</span>
                        </div>
                    @endif
                </div>
                <div class="flex items-center space-x-3">
                    @if(!$showItemMatchingPage)
                        <button wire:click="toggleItemMatchingPage"
                                class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors shadow-sm">
                        <i class="fas fa-wand-magic-sparkles mr-2"></i>
                        Find Matches
                    </button>
                            @endif
                </div>
                        </div>
                            </div>
                        </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if(!$showItemMatchingPage)
            <!-- Unmatched Items Section -->
            <div class="space-y-6">
                <!-- Section Header -->
                <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-100">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                    <div>
                            <h2 class="text-xl font-medium text-gray-900">Your Unmatched Items</h2>
                        <p class="mt-1 text-sm text-gray-500">Items you've reported or searched for that haven't been matched yet</p>
                        </div>
                        <div class="mt-4 sm:mt-0">
                            <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium {{ $hasUnmatchedItems ? 'bg-yellow-50 text-yellow-800' : 'bg-green-50 text-green-800' }}">
                                <i class="fas {{ $hasUnmatchedItems ? 'fa-exclamation-circle' : 'fa-check-circle' }} mr-1.5"></i>
                                {{ $hasUnmatchedItems ? count($unmatchedItems) . ' Items Pending Match' : 'All Items Matched' }}
                            </span>
                        </div>
            </div>
        </div>

                @if($hasUnmatchedItems)
                    <!-- Items Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                        @foreach($unmatchedItems as $item)
                            <div class="group bg-white rounded-lg shadow-sm hover:shadow-md transition-all duration-200 overflow-hidden border border-gray-100 h-[400px] flex flex-col">
                                <!-- Image Container with Fixed Height -->
                                <div class="relative w-full h-48 bg-gray-50 overflow-hidden">
                                    @if($item->images->isNotEmpty())
                                        <img src="{{ Storage::url($item->images->first()->image_path) }}"
                                             alt="{{ $item->title }}"
                                             class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-300">
                                        <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent"></div>
                                    @else
                                        <div class="w-full h-full flex items-center justify-center bg-gray-100">
                                            <i class="fas fa-image text-gray-300 text-4xl"></i>
                                        </div>
                                    @endif

                                    <!-- Status Badge -->
                                    <div class="absolute top-3 left-3">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $item->item_type === 'reported' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800' }}">
                                            <i class="fas {{ $item->item_type === 'reported' ? 'fa-exclamation-circle' : 'fa-search' }} mr-1"></i>
                                            {{ ucfirst($item->item_type) }}
                                        </span>
                                    </div>

                                    <!-- Date Badge -->
                                    <div class="absolute top-3 right-3">
                                        <div class="bg-white/90 backdrop-blur-sm px-3 py-1.5 rounded-lg shadow-sm">
                                            <div class="flex items-center text-sm font-medium text-gray-700">
                                                <i class="fas fa-calendar-alt text-blue-500 mr-1.5"></i>
                                        {{ $item->date_lost ? $item->date_lost->format('M d, Y') : 'Date not specified' }}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Content Section -->
                                <div class="flex-1 p-4 flex flex-col">
                                    <div class="flex-1">
                                        <h3 class="text-lg font-medium text-gray-900 line-clamp-2 group-hover:text-blue-600 transition-colors mb-2">
                                            {{ $item->title }}
                                        </h3>
                                        <p class="text-sm text-gray-600 line-clamp-2 mb-3">
                                            {{ $item->description }}
                                        </p>
                                        <div class="flex flex-wrap gap-2 mb-3">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700">
                                                <i class="fas fa-tag mr-1"></i>
                                                {{ $item->category->name }}
                                            </span>
                                            @if($item->brand)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-50 text-gray-700">
                                                <i class="fas fa-building mr-1"></i>
                                                {{ $item->brand }}
                                            </span>
                                            @endif
                                            @if($item->model)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-50 text-gray-700">
                                                <i class="fas fa-cube mr-1"></i>
                                                {{ $item->model }}
                                            </span>
                                            @endif
                                            @if($item->color)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-50 text-gray-700">
                                                <i class="fas fa-palette mr-1"></i>
                                                {{ $item->color }}
                                            </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="mt-auto">
                                    <div class="flex items-center text-sm text-gray-500">
                                            <i class="fas fa-map-marker-alt text-red-500 mr-1.5"></i>
                                            @if($item->location_type === 'map')
                                                <span class="line-clamp-1">{{ $item->location_address }}</span>
                                            @else
                                                <span class="line-clamp-1">{{ $item->area }} {{ $item->landmarks ? "({$item->landmarks})" : '' }}</span>
                                            @endif
                                        </div>
                                        @if($item->estimated_value)
                                        <div class="flex items-center text-sm text-gray-500 mt-1">
                                            <i class="fas fa-tag text-green-500 mr-1.5"></i>
                                            <span>{{ number_format($item->estimated_value, 2) }} {{ $item->currency }}</span>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <!-- Empty State -->
                    <div class="bg-white rounded-lg shadow-sm p-8 text-center border border-gray-100">
                        <div class="max-w-md mx-auto">
                            <div class="inline-flex items-center justify-center w-16 h-16 bg-green-50 rounded-full mb-4">
                                <i class="fas fa-check-circle text-3xl text-green-500"></i>
                            </div>
                            <h3 class="text-xl font-medium text-gray-900 mb-2">All Items Matched!</h3>
                            <p class="text-gray-500 mb-6">Great news! All your reported and searched items have potential matches.</p>
                            <button wire:click="toggleItemMatchingPage"
                                    class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors shadow-sm">
                                <i class="fas fa-eye mr-2"></i>
                                View Matches
                            </button>
                        </div>
                    </div>
                @endif
            </div>
        @else
            <!-- Matches Section -->
            <div class="space-y-6">
                <!-- Navigation -->
                <div class="flex items-center justify-between">
                    <button wire:click="toggleItemMatchingPage"
                        class="inline-flex items-center text-gray-600 hover:text-gray-900 font-medium">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Items
                    </button>
                </div>

                <!-- Matches Header -->
                <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-100">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h3 class="text-xl font-medium text-gray-900">Potential Matches</h3>
                            <p class="mt-1 text-sm text-gray-500">Found items with similarity score above 60%</p>
                        </div>
                        <div class="mt-4 sm:mt-0 flex flex-col sm:flex-row items-start sm:items-center space-y-3 sm:space-y-0 sm:space-x-4">
                            <!-- Score Legend -->
                            <div class="flex items-center space-x-2">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700">
                                        <i class="fas fa-check-circle mr-1"></i>
                                        80%+
                                    </span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700">
                                        <i class="fas fa-arrow-up mr-1"></i>
                                        60%+
                                    </span>
                            </div>
                            <!-- Controls -->
                            <div class="flex items-center space-x-2">
                                <button wire:click="togglePolling"
                                        class="inline-flex items-center px-3 py-2 bg-white text-sm font-medium text-gray-700 rounded-lg border border-gray-200 hover:bg-gray-50 transition-colors">
                                    <i class="fas {{ $isPolling ? 'fa-pause' : 'fa-play' }} mr-1.5"></i>
                                    {{ $isPolling ? 'Auto' : 'Manual' }}
                                </button>
                                <button wire:click="refreshMatches"
                                        class="inline-flex items-center px-3 py-2 bg-white text-sm font-medium text-gray-700 rounded-lg border border-gray-200 hover:bg-gray-50 transition-colors"
                                        wire:loading.attr="disabled"
                                        wire:loading.class="opacity-50 cursor-not-allowed">
                                    <i class="fas fa-sync-alt mr-1.5" wire:loading.class="animate-spin"></i>
                                    Refresh
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- No Matches Message -->
                @if(empty($matches))
                    <div class="bg-white rounded-xl shadow-sm p-8 text-center">
                        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-blue-100">
                            <i class="fas fa-search text-blue-600"></i>
                        </div>
                        <h3 class="mt-4 text-lg font-medium text-gray-900">No Matches Found Yet</h3>
                        <p class="mt-2 text-sm text-gray-500">
                            We're actively looking for matches with a similarity score above 40%. New items are being reported regularly, so check back soon.
                        </p>
                        <div class="mt-6">
                            <button wire:click="refreshMatches" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <i class="fas fa-sync-alt mr-2"></i>
                                Refresh Matches
                            </button>
                        </div>
                    </div>
                @else
                    <!-- Matches List -->
                    <div class="space-y-6">
                        @foreach($matches as $index => $match)
                            <div class="bg-white rounded-xl shadow-sm overflow-hidden hover:shadow-md transition-shadow duration-200">
                                <div class="p-6">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-4">
                                            <div class="flex-shrink-0">
                                                @if($match['similarity_score'] >= 0.7)
                                                    <div class="h-12 w-12 rounded-full bg-green-100 flex items-center justify-center">
                                                        <i class="fas fa-check-circle text-green-600 text-xl"></i>
                                                    </div>
                                                @elseif($match['similarity_score'] >= 0.5)
                                                    <div class="h-12 w-12 rounded-full bg-yellow-100 flex items-center justify-center">
                                                        <i class="fas fa-star text-yellow-600 text-xl"></i>
                                                    </div>
                                @else
                                                    <div class="h-12 w-12 rounded-full bg-blue-100 flex items-center justify-center">
                                                        <i class="fas fa-search text-blue-600 text-xl"></i>
                                    </div>
                                @endif
                                            </div>
                                            <div>
                                                <h4 class="text-lg font-medium text-gray-900">
                                                    Match Score: {{ number_format($match['similarity_score'] * 100, 1) }}%
                                                </h4>
                                                <p class="text-sm text-gray-500">
                                                    @if($match['similarity_score'] >= 0.7)
                                                        Strong Match
                                                    @elseif($match['similarity_score'] >= 0.5)
                                                        Good Match
                                                    @else
                                                        Potential Match
                                                    @endif
                                                </p>
                                            </div>
                                        </div>
                                        <div class="flex space-x-4">
                                            <button wire:click="showAnalysis({{ $index }})"
                                                class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                                <i class="fas fa-chart-bar mr-2"></i>
                                                Analysis
                                            </button>
                                            <button wire:click="showContact({{ $index }})"
                                                class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                                <i class="fas fa-envelope mr-2"></i>
                                                Contact
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Match Details -->
                                    <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <!-- Reported Item -->
                                        <div class="bg-gray-50 rounded-lg p-4">
                                            <h5 class="font-medium text-gray-900 mb-2">Your Reported Item</h5>
                                            <dl class="space-y-2 text-sm">
                                                <div>
                                                    <dt class="text-gray-500">Title</dt>
                                                    <dd class="font-medium text-gray-900">{{ $match['reported_item']['title'] }}</dd>
                                                </div>
                                                <div>
                                                    <dt class="text-gray-500">Category</dt>
                                                    <dd class="font-medium text-gray-900">{{ $match['reported_item']['category']['name'] ?? 'N/A' }}</dd>
                                                </div>
                                                <div>
                                                    <dt class="text-gray-500">Location</dt>
                                                    <dd class="font-medium text-gray-900">{{ $match['match_details']['location_match']['reported'] ?? 'N/A' }}</dd>
                                                </div>
                                                <div>
                                                    <dt class="text-gray-500">Date</dt>
                                                    <dd class="font-medium text-gray-900">{{ $match['match_details']['date_match']['reported'] ?? 'N/A' }}</dd>
                                </div>
                                            </dl>
                            </div>

                                        <!-- Found Item -->
                                        <div class="bg-gray-50 rounded-lg p-4">
                                            <h5 class="font-medium text-gray-900 mb-2">Found Item</h5>
                                            <dl class="space-y-2 text-sm">
                                                <div>
                                                    <dt class="text-gray-500">Title</dt>
                                                    <dd class="font-medium text-gray-900">{{ $match['found_item']['title'] }}</dd>
                                                </div>
                                                <div>
                                                    <dt class="text-gray-500">Category</dt>
                                                    <dd class="font-medium text-gray-900">{{ $match['found_item']['category']['name'] ?? 'N/A' }}</dd>
                                                </div>
                                                <div>
                                                    <dt class="text-gray-500">Location</dt>
                                                    <dd class="font-medium text-gray-900">{{ $match['match_details']['location_match']['found'] ?? 'N/A' }}</dd>
                                    </div>
                                                <div>
                                                    <dt class="text-gray-500">Date</dt>
                                                    <dd class="font-medium text-gray-900">{{ $match['match_details']['date_match']['found'] ?? 'N/A' }}</dd>
                                    </div>
                                            </dl>
                                </div>
                                    </div>

                                    <!-- Matching Attributes -->
                                    <div class="mt-6">
                                        <h5 class="font-medium text-gray-900 mb-2">Matching Attributes</h5>
                                        <div class="flex flex-wrap gap-2">
                                            @foreach($match['match_details']['attributes_match'] as $attribute => $matches)
                                                @if($matches)
                                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                                        <i class="fas fa-check-circle mr-1"></i>
                                                        {{ ucfirst($attribute) }}
                                                    </span>
                                                @endif
                                            @endforeach
                                    </div>
                                </div>
                                </div>
                            </div>
                        @endforeach
                </div>
                @endif
            </div>
        @endif
    </div>

    <!-- Loading Indicator -->
    <div wire:loading wire:target="refreshMatches, toggleItemMatchingPage"
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 max-w-sm w-full mx-4">
            <div class="flex items-center justify-center space-x-3">
                <div class="animate-spin rounded-full h-8 w-8 border-4 border-blue-500 border-t-transparent"></div>
                <span class="text-gray-700">{{ $loadingMessage ?: 'Loading...' }}</span>
            </div>
        </div>
    </div>

    <!-- Include Modal Partials -->
    @include('livewire.partials.analysis-modal')
    @include('livewire.partials.contact-modal')
    @include('livewire.partials.claim-modal')
</div>

@script
<script>
    // Initialize Tippy.js tooltips
    document.addEventListener('livewire:init', () => {
        function initializeTippy() {
            tippy('[data-tippy-content]', {
                theme: 'light',
                animation: 'scale',
                placement: 'top',
                arrow: true,
                delay: [100, 200]
            });
        }

        // Initialize on first load
        initializeTippy();

        // Re-initialize after Livewire updates
        Livewire.on('contentChanged', () => {
            initializeTippy();
        });

        // Handle polling toggle
        let pollInterval;

        function startPolling() {
            pollInterval = setInterval(() => {
                @this.checkForUpdates();
            }, @this.pollInterval);
        }

        function stopPolling() {
            if (pollInterval) {
                clearInterval(pollInterval);
            }
        }

        // Start polling if enabled
        if (@this.isPolling) {
            startPolling();
        }

        // Listen for polling toggle events
        Livewire.on('polling-toggled', ({ isPolling }) => {
            if (isPolling) {
                startPolling();
            } else {
                stopPolling();
            }
        });

        // Clean up on disconnect
        document.addEventListener('disconnect', () => {
            stopPolling();
        });
    });
</script>
@endscript
