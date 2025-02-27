<!-- Professional Modern UI -->
<div class="min-h-screen bg-gradient-to-b from-gray-50 to-gray-100">
    <!-- Header Section -->
    <div class="bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <span class="text-2xl font-semibold text-gray-800">
                        <i class="fas fa-search-location text-blue-600 mr-2"></i>
                        Item Matching
                    </span>
                </div>
    @if (!$showItemMatchingPage)
                    <button
                        wire:click="toggleItemMatchingPage"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-all duration-150 shadow-sm hover:shadow focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fas fa-wand-magic-sparkles mr-2"></i>
                        Find Matches
                    </button>
                            @endif
                        </div>
                            </div>
                        </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if (!$showItemMatchingPage)
            <!-- Unmatched Items Section -->
            <div class="space-y-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between bg-white p-4 rounded-lg shadow-sm border border-gray-200">
                    <div>
                        <h2 class="text-xl font-semibold text-gray-900">Your Unmatched Items</h2>
                        <p class="mt-1 text-sm text-gray-500">Items that haven't been matched with any found items yet</p>
                    </div>
                    <div class="mt-3 sm:mt-0 flex items-center text-sm text-gray-500">
                        <i class="fas fa-history mr-2"></i>
                        Last updated: {{ now()->diffForHumans() }}
            </div>
        </div>

                @if($hasUnmatchedItems)
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                        @foreach ($unmatchedItems as $item)
                            <div class="group bg-white rounded-xl shadow-sm hover:shadow-md transition-all duration-300 overflow-hidden border border-gray-200">
                                <!-- Image Section -->
                                <div class="relative aspect-w-16 aspect-h-10">
                                    @if ($item->images->isNotEmpty())
                                        <img src="{{ Storage::url($item->images->first()->image_path) }}"
                                             alt="{{ $item->title }}"
                                             class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-300"
                                             data-tippy-content="Click to view full image">
                                    @else
                                        <div class="w-full h-full bg-gray-100 flex items-center justify-center">
                                            <i class="fas fa-image text-gray-300 text-4xl"></i>
                                        </div>
                                    @endif
                                    <div class="absolute top-3 right-3 bg-white/90 backdrop-blur-sm px-2 py-1 rounded-lg text-sm font-medium text-gray-700 shadow-sm">
                                        <i class="fas fa-calendar-alt mr-1.5 text-blue-500"></i>
                                        {{ $item->date_lost ? $item->date_lost->format('M d, Y') : 'Date not specified' }}
                                    </div>
                                </div>

                                <!-- Content Section -->
                                <div class="p-4">
                                    <div class="mb-3">
                                        <h3 class="text-lg font-medium text-gray-900 line-clamp-1 group-hover:text-blue-600 transition-colors">
                                            {{ $item->title }}
                                        </h3>
                                        <div class="mt-1 flex items-center text-sm text-gray-600">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                <i class="fas fa-tag mr-1"></i>
                                                {{ $item->category->name }}
                                            </span>
                                        </div>
                                    </div>

                                    <div class="flex items-center justify-between text-sm text-gray-600">
                                        <div class="flex items-center">
                                            <i class="fas fa-map-marker-alt text-red-500 mr-1.5"></i>
                                            <span class="line-clamp-1">{{ $item->location }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8">
                                <div class="text-center">
                            <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-100 rounded-full mb-4">
                                <i class="fas fa-check-circle text-3xl text-blue-600"></i>
                            </div>
                            <h3 class="text-xl font-medium text-gray-900 mb-2">All Items Matched!</h3>
                            <p class="text-gray-600 mb-4">Great news! All your reported items have potential matches.</p>
                            <button
                                wire:click="toggleItemMatchingPage"
                                class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-all duration-150 shadow-sm hover:shadow focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <i class="fas fa-eye mr-2"></i>
                                View Matches
                            </button>
                        </div>
                    </div>
                @endif
            </div>
        @else
            <!-- Matched Items Section -->
            <div class="space-y-6">
                <div class="flex items-center justify-between">
                    <button
                        wire:click="toggleItemMatchingPage"
                        class="inline-flex items-center text-gray-600 hover:text-gray-900 font-medium">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Items
                    </button>
                </div>

                <!-- Matches Header -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h3 class="text-xl font-semibold text-gray-900">Potential Matches</h3>
                            <p class="mt-1 text-sm text-gray-500">Found items with similarity score above 60%</p>
                        </div>
                        <div class="mt-3 sm:mt-0 flex items-center space-x-4">
                            <div class="text-sm text-gray-500 flex items-center space-x-2">
                                <div class="flex items-center space-x-1">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                                        <i class="fas fa-check-circle mr-1"></i>
                                        80%+
                                    </span>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        <i class="fas fa-arrow-up mr-1"></i>
                                        60%+
                                    </span>
                                </div>
                                <i class="fas fa-clock mr-1.5"></i>
                                <span x-data="{ timestamp: '' }" x-init="timestamp = new Date().toLocaleTimeString()" x-text="timestamp"></span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <button
                                    wire:click="togglePolling"
                                    class="inline-flex items-center px-3 py-2 bg-white text-sm font-medium text-gray-700 rounded-lg border border-gray-300 hover:bg-gray-50 transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                                    data-tippy-content="{{ $isPolling ? 'Disable auto-refresh' : 'Enable auto-refresh' }}">
                                    <i class="fas {{ $isPolling ? 'fa-pause' : 'fa-play' }} mr-1.5"></i>
                                    {{ $isPolling ? 'Auto' : 'Paused' }}
                                </button>
                                <button
                                    wire:click="refreshMatches"
                                    class="inline-flex items-center px-3 py-2 bg-white text-sm font-medium text-gray-700 rounded-lg border border-gray-300 hover:bg-gray-50 transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                                    :class="{ 'opacity-50 cursor-not-allowed': $wire.isLoading }"
                                    :disabled="$wire.isLoading"
                                    data-tippy-content="Check for new matches">
                                    <i class="fas fa-sync-alt mr-1.5" :class="{ 'animate-spin': $wire.isLoading }"></i>
                                    Refresh
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Matches Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($matches as $index => $match)
                        <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition-all duration-300 overflow-hidden border border-gray-200">
                            <!-- Image Section -->
                            <div class="relative aspect-w-16 aspect-h-10 bg-gray-50 overflow-hidden group">
                                @if ($match['reported_item']->images->isNotEmpty())
                                    <img src="{{ Storage::url($match['reported_item']->images->first()->image_path) }}"
                                        alt="{{ $match['reported_item']->title }}"
                                        class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-300"
                                        data-tippy-content="Click to view full image">
                                @else
                                    <div class="w-full h-full flex items-center justify-center">
                                        <i class="fas fa-image text-gray-300 text-4xl"></i>
                                    </div>
                                @endif
                                <!-- Match Score Badge -->
                                <div class="absolute top-3 right-3 px-3 py-1.5 rounded-lg bg-white shadow-lg backdrop-blur-sm bg-opacity-90">
                                    <div class="flex items-center space-x-1.5">
                                        <div class="flex items-center">
                                            <i class="fas fa-percentage text-sm {{ $match['similarity_score'] >= 0.8 ? 'text-emerald-500' : 'text-blue-500' }}"></i>
                                            <span class="ml-1.5 font-semibold {{ $match['similarity_score'] >= 0.8 ? 'text-emerald-700' : 'text-blue-700' }}">
                                                {{ number_format($match['similarity_score'] * 100, 0) }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Content Section -->
                            <div class="p-4 space-y-4">
                                <div>
                                    <h4 class="text-lg font-medium text-gray-900 line-clamp-1 group-hover:text-blue-600 transition-colors">
                                        {{ $match['reported_item']->title }}
                                    </h4>
                                    <div class="mt-1.5 flex items-center">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            <i class="fas fa-tag mr-1"></i>
                                            {{ $match['reported_item']->category->name }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="flex items-center justify-between pt-2 border-t border-gray-100">
                                    <div class="flex -space-x-1">
                                        <button
                                            wire:click="showAnalysis({{ $index }})"
                                            class="relative inline-flex items-center justify-center w-10 h-10 rounded-lg bg-gray-50 hover:bg-blue-50 text-gray-500 hover:text-blue-600 transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                                            data-tippy-content="View match analysis">
                                            <i class="fas fa-chart-bar"></i>
                                        </button>
                                        <button
                                            wire:click="showContact({{ $index }})"
                                            class="relative inline-flex items-center justify-center w-10 h-10 rounded-lg bg-gray-50 hover:bg-green-50 text-gray-500 hover:text-green-600 transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                                            data-tippy-content="Contact finder">
                                            <i class="fas fa-envelope"></i>
                                        </button>
                                    </div>
                                    <button
                                        wire:click="showClaim({{ $index }})"
                                        class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        <i class="fas fa-hand-holding mr-1.5"></i>
                                        Claim Item
                                    </button>
                                </div>
                                </div>
                            </div>
                        @endforeach
                </div>

                @if(count($matches) === 0)
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8">
                        <div class="text-center">
                            <div class="inline-flex items-center justify-center w-16 h-16 bg-gray-100 rounded-full mb-4">
                                <i class="fas fa-search text-3xl text-gray-400"></i>
                </div>
                            <h3 class="text-xl font-medium text-gray-900 mb-2">No Strong Matches Found</h3>
                            <p class="text-gray-600 mb-4">We couldn't find any items with a similarity score above 60%. Keep checking back as new items are reported.</p>
            </div>
        </div>
    @endif
            </div>
        @endif
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
                document.querySelector('[x-data="{ timestamp: \'\' }"]').__x.$data.timestamp = new Date().toLocaleTimeString();
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
