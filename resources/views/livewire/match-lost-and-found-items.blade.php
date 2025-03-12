<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header Section -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Lost and Found Item Matching</h1>
            <p class="mt-2 text-sm text-gray-600">Find potential matches for your lost or found items using our advanced matching system.</p>

            <!-- Auto-matching toggle -->
            <div class="mt-4 flex items-center space-x-2">
                <button
                    wire:click="toggleAutoMatch"
                    class="inline-flex items-center px-4 py-2 border rounded-md font-semibold text-xs uppercase tracking-widest focus:outline-none focus:ring-2 focus:ring-offset-2 transition ease-in-out duration-150 {{ $autoMatchEnabled ? 'bg-green-600 hover:bg-green-700 text-white border-transparent' : 'bg-gray-200 hover:bg-gray-300 text-gray-700 border-gray-300' }}"
                >
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $autoMatchEnabled ? 'M5 13l4 4L19 7' : 'M6 18L18 6M6 6l12 12' }}"/>
                    </svg>
                    {{ $autoMatchEnabled ? 'Auto-Matching Enabled' : 'Auto-Matching Disabled' }}
                </button>
                <span class="text-sm text-gray-500">
                    {{ $autoMatchEnabled ? 'System is automatically finding matches' : 'Click to enable automatic matching' }}
                </span>
            </div>
        </div>

        <!-- Filters and Search Section -->
        <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex-1 min-w-0">
                    <div class="relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <input wire:model.live.debounce.300ms="searchQuery" type="text" class="focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 pr-12 sm:text-sm border-gray-300 rounded-md" placeholder="Search items...">
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <select wire:model.live="selectedCategory" class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>

                    <select wire:model.live="itemsPerPage" class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                        <option value="9">9 per page</option>
                        <option value="18">18 per page</option>
                        <option value="27">27 per page</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Tabs -->
        <div class="mb-6">
            <nav class="flex space-x-4" aria-label="Tabs">
                <button wire:click="$set('selectedTab', 'unmatched')"
                    class="@if($selectedTab === 'unmatched') bg-blue-100 text-blue-700 @else text-gray-500 hover:text-gray-700 @endif px-3 py-2 font-medium text-sm rounded-md">
                    Unmatched Items
                </button>
                <button wire:click="$set('selectedTab', 'matched')"
                    class="@if($selectedTab === 'matched') bg-blue-100 text-blue-700 @else text-gray-500 hover:text-gray-700 @endif px-3 py-2 font-medium text-sm rounded-md">
                    Matched Items
                </button>
            </nav>
        </div>

        <!-- Items Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @if($selectedTab === 'unmatched')
                @forelse($unmatchedItems as $item)
                    <div class="bg-white rounded-lg shadow-sm overflow-hidden hover:shadow-md transition-shadow duration-200"
                         x-data="{ showDetails: false }">
                        <div class="relative">
                            @if($item->images->isNotEmpty())
                                <img src="{{ $item->images->first()->url }}"
                                     alt="{{ $item->title }}"
                                     class="w-full h-48 object-cover">
        @else
                                <div class="w-full h-48 bg-gray-100 flex items-center justify-center">
                                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                            @endif

                            <!-- Processing Overlay -->
                        @if($isLoading && $processingItemId === $item->id)
                                <div class="absolute inset-0 bg-white bg-opacity-90 flex flex-col items-center justify-center">
                                    <div class="space-y-4 text-center">
                                        <div class="relative">
                                            <div class="w-16 h-16">
                                                <div class="absolute top-0 left-0 w-full h-full border-4 border-blue-200 rounded-full animate-spin"></div>
                                                <div class="absolute top-0 left-0 w-full h-full border-4 border-blue-500 rounded-full animate-spin" style="border-top-color: transparent; animation-duration: 1.5s;"></div>
                                            </div>
                                            <div class="mt-4">
                                                <div class="w-48 bg-gray-200 rounded-full h-2">
                                                    <div class="bg-blue-500 h-2 rounded-full transition-all duration-300 ease-in-out"
                                                         style="width: {{ $progress }}%"></div>
                                                </div>
                                                <p class="mt-2 text-sm font-medium text-gray-900">{{ $processingStage }}</p>
                                                <p class="text-xs text-gray-500">{{ $progress }}% Complete</p>
                                    </div>
                                    </div>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="p-4">
                            <div class="flex items-start justify-between">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">{{ $item->title }}</h3>
                                    <p class="text-sm text-gray-500">{{ $item->category->name }}</p>
                                </div>
                                <button @click="showDetails = !showDetails"
                                        class="ml-4 text-gray-400 hover:text-gray-500">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              :d="showDetails ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7'"/>
                                    </svg>
                                </button>
                            </div>

                            <div x-show="showDetails"
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 transform -translate-y-2"
                                 x-transition:enter-end="opacity-100 transform translate-y-0"
                                 x-transition:leave="transition ease-in duration-150"
                                 x-transition:leave-start="opacity-100 transform translate-y-0"
                                 x-transition:leave-end="opacity-0 transform -translate-y-2"
                                 class="mt-4">
                        <p class="text-sm text-gray-600">{{ $item->description }}</p>
                                <div class="mt-4 flex items-center text-sm text-gray-500">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    {{ $item->created_at->format('M d, Y') }}
                                </div>
                            </div>

                            <div class="mt-4">
                                <button wire:click="findMatchesForItem({{ $item->id }})"
                                        class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-150"
                                        @if($isLoading) disabled @endif>
                                    @if($isLoading && $processingItemId === $item->id)
                                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        Processing...
                                    @else
                                        Find Matches
                                    @endif
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full">
                        <div class="text-center py-12 bg-white rounded-lg">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No unmatched items</h3>
                            <p class="mt-1 text-sm text-gray-500">All your items have potential matches!</p>
                        </div>
                    </div>
                @endforelse
            @else
                @forelse($matchedItems as $item)
                    <div class="bg-white rounded-lg shadow-sm overflow-hidden hover:shadow-md transition-shadow duration-200"
                         x-data="{ showDetails: false }">
                        <div class="relative">
                        @if($item->images->isNotEmpty())
                                <img src="{{ $item->images->first()->url }}"
                                     alt="{{ $item->title }}"
                                     class="w-full h-48 object-cover">
                            @else
                                <div class="w-full h-48 bg-gray-100 flex items-center justify-center">
                                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                            @endif
                            <div class="absolute top-2 right-2">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Matched
                                </span>
                            </div>
                        </div>

                        <div class="p-4">
                            <div class="flex items-start justify-between">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">{{ $item->title }}</h3>
                                    <p class="text-sm text-gray-500">{{ $item->category->name }}</p>
                                </div>
                                <button @click="showDetails = !showDetails"
                                        class="ml-4 text-gray-400 hover:text-gray-500">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              :d="showDetails ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7'"/>
                                    </svg>
                        </button>
                            </div>

                            <div x-show="showDetails"
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 transform -translate-y-2"
                                 x-transition:enter-end="opacity-100 transform translate-y-0"
                                 x-transition:leave="transition ease-in duration-150"
                                 x-transition:leave-start="opacity-100 transform translate-y-0"
                                 x-transition:leave-end="opacity-0 transform -translate-y-2"
                                 class="mt-4">
                                <p class="text-sm text-gray-600">{{ $item->description }}</p>
                                <div class="mt-4 flex items-center justify-between text-sm text-gray-500">
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        {{ $item->created_at->format('M d, Y') }}
                                    </div>
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                        </svg>
                                        {{ $item->matches->count() }} matches
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4">
                                <a href="{{ route('matches.show', $item) }}"
                                   class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors duration-150">
                                    View Matches
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full">
                        <div class="text-center py-12 bg-white rounded-lg">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No matched items</h3>
                            <p class="mt-1 text-sm text-gray-500">Try finding matches for your unmatched items!</p>
                        </div>
            </div>
                @endforelse
        @endif
    </div>

        <!-- Pagination -->
        <div class="mt-6">
            @if($selectedTab === 'unmatched')
                {{ $unmatchedItems->links() }}
        @else
                {{ $matchedItems->links() }}
                        @endif
        </div>
    </div>

    <!-- Notifications -->
    <div x-data="{ notifications: [] }"
         @notify.window="notifications.push({
             id: Date.now(),
             type: $event.detail.type,
             message: $event.detail.message
         }); setTimeout(() => notifications.shift(), 3000)"
         class="fixed bottom-0 right-0 p-4 space-y-4 z-50">
        <template x-for="notification in notifications" :key="notification.id">
            <div x-show="true"
                 x-transition:enter="transform ease-out duration-300 transition"
                 x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
                 x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
                 x-transition:leave="transition ease-in duration-100"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 :class="{
                     'bg-green-500': notification.type === 'success',
                     'bg-blue-500': notification.type === 'info',
                     'bg-red-500': notification.type === 'error'
                 }"
                 class="max-w-sm w-full shadow-lg rounded-lg pointer-events-auto">
                <div class="p-4">
                    <div class="flex items-center">
                        <div class="flex-1 w-0">
                            <p class="text-sm font-medium text-white" x-text="notification.message"></p>
                        </div>
                        <div class="ml-4 flex-shrink-0 flex">
                            <button @click="notifications = notifications.filter(n => n.id !== notification.id)"
                                    class="inline-flex text-white hover:text-gray-200">
                                <span class="sr-only">Close</span>
                                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('progressUpdate', ({ progress, stage }) => {
            // Additional progress animations can be added here if needed
        });

        Livewire.on('matchesFound', ({ count, itemId }) => {
            // Additional match found animations or interactions can be added here
        });

        // Handle polling
        Livewire.on('scheduleNextPoll', () => {
            setTimeout(() => {
                @this.dispatch('pollMatches');
            }, {{ $this::POLLING_INTERVAL }});
        });
    });
</script>
@endpush
