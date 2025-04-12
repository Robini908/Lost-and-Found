@php
    use App\Models\LostItem;
@endphp

<div x-data="{
    focused: false,
    init() {
        this.$watch('$wire.query', value => {
            if (!value) {
                $wire.showResults = false;
            }
        });
    }
}"
    @click.away="focused = false"
    class="relative">

    <div class="relative">
        <input
            wire:model.live.debounce.300ms="query"
            type="search"
            placeholder="Search by title, description, location..."
            class="w-full sm:w-64 pl-10 pr-4 py-2 text-sm bg-gray-100 border border-transparent rounded-lg focus:outline-none focus:bg-white focus:border-blue-300 transition duration-150"
            @focus="focused = true"
            @keydown.escape.window="focused = false"
            @keydown.tab="focused = false"
            @keydown.enter.prevent="if($wire.query && $wire.searchResults.length) $wire.navigateToResult($wire.searchResults[0]?.url)"
        >
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <svg class="h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
            </svg>
        </div>
        @if($query)
            <button
                wire:click="clearSearch"
                class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500 hover:text-gray-700"
                title="Clear search">
                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                </svg>
            </button>
        @endif
    </div>

    <!-- Search Results Dropdown -->
    <div
        x-show="focused && $wire.showResults && $wire.query.length >= 2"
        x-cloak
        class="absolute mt-2 w-full bg-white rounded-lg shadow-lg border border-gray-200 overflow-hidden z-50">

        <!-- Results Found -->
        @if(count($searchResults) > 0)
            <div class="max-h-96 overflow-y-auto">
                @foreach($searchResults as $result)
                    <button
                        wire:click="navigateToResult('{{ $result['url'] }}')"
                        class="w-full px-4 py-3 hover:bg-gray-50 flex items-center space-x-4 text-left transition duration-150">
                        <!-- Item Image -->
                        <div class="flex-shrink-0 w-12 h-12 bg-gray-100 rounded-lg overflow-hidden">
                            @if($result['image'])
                                <img src="{{ Storage::url($result['image']) }}"
                                     alt="{{ $result['title'] }}"
                                     class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center">
                                    <i class="fas fa-image text-gray-400"></i>
                                </div>
                            @endif
                        </div>

                        <!-- Item Details -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between">
                                <p class="text-sm font-medium text-gray-900 truncate">
                                    {!! $result['highlight'] !!}
                                </p>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{
                                    $result['type'] === 'Found' ? 'bg-green-100 text-green-800' :
                                    ($result['type'] === 'Searched' ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800')
                                }}">
                                    {{ $result['type'] }}
                                </span>
                            </div>
                            <div class="mt-1 flex items-center text-xs text-gray-500 space-x-2">
                                @if($result['category'])
                                    <span class="truncate">{{ $result['category'] }}</span>
                                @endif

                                @if($result['location_address'] || $result['area'])
                                    <span class="text-gray-400">•</span>
                                    <span class="truncate">
                                        {{ $result['location_address'] ?? $result['area'] }}
                                    </span>
                                @endif

                                @if($result['value'])
                                    <span class="text-gray-400">•</span>
                                    <span>{{ number_format($result['value'], 2) }} {{ $result['currency'] ?? 'USD' }}</span>
                                @endif
                            </div>
                            <div class="mt-1 flex items-center text-xs space-x-2">
                                <span class="text-gray-400">
                                    {{ \Carbon\Carbon::parse($result['created_at'])->diffForHumans() }}
                                </span>
                                @if($result['is_verified'])
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700">
                                        <i class="fas fa-check-circle mr-1"></i>
                                        Verified
                                    </span>
                                @endif
                            </div>
                        </div>
                    </button>
                @endforeach
            </div>
        @else
            <!-- No Results -->
            @if(strlen($query) >= 2)
                <div class="px-4 py-6 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p class="mt-4 text-sm text-gray-900">No results found for "{{ $query }}"</p>
                    <p class="mt-2 text-xs text-gray-500">Try adjusting your search terms or filters.</p>
                </div>
            @endif
        @endif
    </div>

    <!-- Loading State -->
    <div wire:loading wire:target="query" class="absolute right-0 top-0 mt-2 mr-3">
        <svg class="animate-spin h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
    </div>
</div>
