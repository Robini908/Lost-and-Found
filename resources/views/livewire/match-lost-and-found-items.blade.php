<div>
    <!-- Banner for Real-Time Matches -->
    @if ($showBanner)
        <x-banner :message="$bannerMessage" />
    @endif

    <!-- Default Page with Unmatched Items -->
    @if (!$showItemMatchingPage)
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <h2 class="text-2xl font-bold mb-6">Your Unmatched Items</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @foreach ($unmatchedItems as $item)
                    <div
                        class="bg-white rounded-lg shadow-md border border-gray-100 hover:shadow-lg transition-shadow duration-300 overflow-hidden">
                        <!-- Item Image -->
                        <div class="relative h-48 w-full">
                            @if ($item->images->isNotEmpty())
                                <img src="{{ Storage::url($item->images->first()->image_path) }}"
                                    alt="{{ $item->title }}" class="w-full h-full object-cover" />
                            @else
                                <div class="w-full h-full bg-gray-200 flex items-center justify-center text-gray-500">
                                    <i class="fas fa-image fa-2x"></i>
                                </div>
                            @endif
                        </div>
                        <!-- Item Details -->
                        <div class="p-4">
                            <h3 class="text-xl font-semibold text-gray-800 mb-2 truncate">{{ $item->title }}</h3>
                            <div class="flex items-center text-sm text-gray-600 mb-2">
                                <i class="fas fa-map-marker-alt mr-2"></i>
                                <span>{{ $item->location }}</span>
                            </div>
                            <div class="flex items-center text-sm text-gray-600 mb-2">
                                <i class="fas fa-calendar-alt mr-2"></i>
                                <span>Lost on
                                    {{ $item->date_lost ? $item->date_lost->format('M d, Y') : 'Date not specified' }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <button wire:click="toggleItemMatchingPage"
                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mt-6">
                Go to Item Matching
            </button>
        </div>
    @endif

    <!-- Item Matching Page -->
    @if ($showItemMatchingPage)
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4 relative">
            <!-- Close Button -->
            <button wire:click="toggleItemMatchingPage"
                class="absolute top-0 right-0 p-4 text-gray-600 hover:text-gray-900">
                <i class="fas fa-times fa-2x"></i>
            </button>

            <h2 class="text-2xl font-bold mb-6">Item Matching</h2>
            <p class="text-gray-600 mb-6">
                This page allows you to find matches for your reported items. Click the button below to start the
                matching process.
            </p>

            <!-- Button to Initiate Matching -->
            <button wire:click="findMatches" wire:loading.attr="disabled"
                class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                <span wire:loading.remove>Initiate Matching</span>
                <span wire:loading>
                    <i class="fas fa-spinner fa-spin"></i> Analyzing...
                </span>
            </button>

            <!-- Matched Items Section -->
            @if ($showMatches)
                <div class="mt-8">
                    <h3 class="text-xl font-bold mb-4">Matched Items</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                        @foreach ($matches as $match)
                            <div
                                class="bg-gradient-to-r from-blue-50 to-purple-50 rounded-lg shadow-md border border-gray-100 hover:shadow-lg transition-shadow duration-300 overflow-hidden">
                                <!-- Reported Item Image -->
                                <div class="relative h-48 w-full">
                                    @if ($match['reported_item']->images->isNotEmpty())
                                        <img src="{{ Storage::url($match['reported_item']->images->first()->image_path) }}"
                                            alt="{{ $match['reported_item']->title }}"
                                            class="w-full h-full object-cover" />
                                    @else
                                        <div
                                            class="w-full h-full bg-gray-200 flex items-center justify-center text-gray-500">
                                            <i class="fas fa-image fa-2x"></i>
                                        </div>
                                    @endif
                                </div>
                                <!-- Reported Item Details -->
                                <div class="p-4">
                                    <h3 class="text-xl font-semibold text-gray-800 mb-2 truncate">
                                        {{ $match['reported_item']->title }}</h3>
                                    <div class="flex items-center text-sm text-gray-600 mb-2">
                                        <i class="fas fa-map-marker-alt mr-2"></i>
                                        <span>{{ $match['reported_item']->location }}</span>
                                    </div>
                                    <div class="flex items-center text-sm text-gray-600 mb-2">
                                        <i class="fas fa-calendar-alt mr-2"></i>
                                        <span>Lost on
                                            {{ $match['reported_item']->date_lost ? $match['reported_item']->date_lost->format('M d, Y') : 'Date not specified' }}</span>
                                    </div>
                                </div>
                                <!-- Arrow -->
                                <div class="text-center">
                                    <i class="fas fa-arrow-right text-2xl text-gray-400"></i>
                                </div>
                                <!-- Found Item Details -->
                                <div class="p-4">
                                    <h3 class="text-xl font-semibold text-gray-800 mb-2 truncate">
                                        {{ $match['found_item']->title }}</h3>
                                    <div class="flex items-center text-sm text-gray-600 mb-2">
                                        <i class="fas fa-map-marker-alt mr-2"></i>
                                        <span>{{ $match['found_item']->location }}</span>
                                    </div>
                                    <div class="flex items-center text-sm text-gray-600 mb-2">
                                        <i class="fas fa-calendar-alt mr-2"></i>
                                        <span>Found on
                                            {{ $match['found_item']->date_found ? $match['found_item']->date_found->format('M d, Y') : 'Date not specified' }}</span>
                                    </div>
                                    <!-- Found By -->
                                    <div class="flex items-center text-sm text-gray-600 mb-2">
                                        <i class="fas fa-user mr-2"></i>
                                        <span>Found by: {{ $match['found_item']->found_by_name ?? 'Unknown' }}</span>
                                    </div>
                                </div>
                                <!-- Match Information -->
                                <div class="p-4">
                                    <p class="text-sm text-gray-600">Match Information:</p>
                                    <p class="text-sm text-gray-600">Similarity Score:
                                        {{ number_format($match['similarity_score'] * 100, 2) }}%</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    @endif

    <!-- Analysis Modal -->
    @if ($showAnalysisModal)
        <div class="fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-50 z-50">
            <div class="bg-white p-8 rounded-xl shadow-2xl text-center max-w-md mx-4">
                <p class="text-gray-600 animate-pulse">{{ $loadingMessage }}</p>
                <div class="w-full bg-gray-200 rounded-full h-2.5 mt-4">
                    <div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ $progress }}%"></div>
                </div>
            </div>
        </div>
    @endif
</div>
