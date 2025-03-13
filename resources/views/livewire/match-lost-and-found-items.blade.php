<div class="min-h-screen bg-gray-50">
    <!-- Tabs -->
    <div class="bg-white shadow-sm mb-6">
        <div class="max-w-7xl mx-auto">
            <div class="border-b border-gray-200">
                <nav class="-mb-px flex" aria-label="Tabs">
                    <button
                        wire:click="setActiveTab('searching')"
                        class="w-1/2 py-4 px-1 text-center border-b-2 font-medium text-sm {{ $activeTab === 'searching'
                            ? 'border-blue-500 text-blue-600'
                            : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}"
                    >
                        Searching Items
                    </button>
                    <button
                        wire:click="setActiveTab('found')"
                        class="w-1/2 py-4 px-1 text-center border-b-2 font-medium text-sm {{ $activeTab === 'found'
                            ? 'border-blue-500 text-blue-600'
                            : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}"
                    >
                        Found Matches
                        @if($foundMatches->isNotEmpty())
                            <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ $foundMatches->count() }}
                            </span>
                        @endif
                    </button>
                </nav>
            </div>
        </div>
    </div>

    <!-- Search and Filters (Only show in searching tab) -->
    @if($activeTab === 'searching')
        <!-- Search Header -->
        <div class="sticky top-0 z-10 bg-white shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                <div class="flex items-center space-x-4">
                    <!-- Search Bar -->
                    <div class="flex-1">
                        <div class="relative rounded-full shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <input wire:model.live.debounce.300ms="search" type="text" class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-full leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="Search lost items...">
                        </div>
                    </div>

                    <!-- Filters -->
                    <div class="flex items-center space-x-4">
                        <!-- Category Filter -->
                        <select wire:model.live="selectedCategory" class="rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">All Categories</option>
                            @foreach(\App\Models\Category::all() as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>

                        <!-- Date Range Filter -->
                        <input wire:model.live="dateRange" type="text" class="rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500" placeholder="Date Range" x-data x-init="flatpickr($el, {mode: 'range', dateFormat: 'Y-m-d'})">
                    </div>
                </div>
            </div>
        </div>

        <!-- Item Type Filter -->
        <div class="bg-white shadow-sm mb-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16">
                    <div class="flex items-center space-x-4">
                        @foreach($itemTypes as $value => $label)
                            <button
                                wire:click="setItemTypeFilter('{{ $value }}')"
                                class="px-3 py-2 rounded-md text-sm font-medium {{ $itemTypeFilter === $value
                                    ? 'bg-blue-100 text-blue-700'
                                    : 'text-gray-500 hover:text-gray-700 hover:bg-gray-100' }}"
                            >
                                {{ $label }}
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if($activeTab === 'searching')
            <!-- Searching View -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Lost Items List -->
                <div class="bg-white rounded-lg shadow">
                    <div class="px-4 py-5 border-b border-gray-200 sm:px-6">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">
                                Your {{ ucfirst($itemTypeFilter) }} Items
                            </h3>
                            <span class="text-sm text-gray-500">
                                {{ $lostItems->total() }} items
                            </span>
                        </div>
                    </div>
                    <ul class="divide-y divide-gray-200 overflow-hidden">
                        @forelse($lostItems as $item)
                            <li class="relative hover:bg-gray-50 transition-colors duration-200 {{ $selectedItem === $item->id ? 'bg-blue-50' : '' }}">
                                <div class="px-4 py-4 sm:px-6 cursor-pointer" wire:click="findMatches({{ $item->id }})">
                                    <div class="flex items-center justify-between">
                                        <div class="flex-1 min-w-0">
                                            <h4 class="text-sm font-medium text-blue-600 truncate">{{ $item->title }}</h4>
                                            <p class="mt-1 text-sm text-gray-500 line-clamp-2">{{ $item->description }}</p>
                                        </div>
                                        @if($item->images->isNotEmpty())
                                            <img src="{{ $item->images->first()->url }}" alt="{{ $item->title }}" class="h-12 w-12 rounded-lg object-cover ml-4">
                                        @endif
                                    </div>
                                    <div class="mt-2 flex items-center text-xs text-gray-500">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $item->category->color ?? 'bg-gray-100' }}">
                                            {{ $item->category->name }}
                                        </span>
                                        <span class="ml-2">{{ $item->date_lost->format('M d, Y') }}</span>
                                    </div>
                                </div>
                            </li>
                        @empty
                            <li class="px-4 py-8 text-center text-gray-500">
                                No lost items found.
                            </li>
                        @endforelse
                    </ul>
                    <div class="px-4 py-3 border-t border-gray-200">
                        {{ $lostItems->links() }}
                    </div>
                </div>

                <!-- Matched Items -->
                <div class="bg-white rounded-lg shadow">
                    <div class="px-4 py-5 border-b border-gray-200 sm:px-6">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">
                                Potential Matches
                            </h3>
                            @if($loadingMatches)
                                <div class="animate-pulse flex items-center text-sm text-blue-600">
                                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Finding matches...
                                </div>
                            @endif
                        </div>
                    </div>
                    @if($selectedItem)
                        <ul class="divide-y divide-gray-200 overflow-hidden" wire:poll.10s>
                            @forelse($matchedItems as $match)
                                <li class="relative hover:bg-gray-50">
                                    <div class="px-4 py-4 sm:px-6">
                                        <div class="flex items-center justify-between">
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center justify-between">
                                                    <h4 class="text-sm font-medium text-blue-600 truncate">{{ $match['item']->title }}</h4>
                                                    <div class="ml-2 flex-shrink-0">
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $match['similarity'] >= 0.7 ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                                            {{ number_format($match['similarity'] * 100, 0) }}% Match
                                                        </span>
                                                    </div>
                                                </div>
                                                <p class="mt-1 text-sm text-gray-500 line-clamp-2">{{ $match['item']->description }}</p>
                                            </div>
                                            @if($match['item']->images->isNotEmpty())
                                                <img src="{{ $match['item']->images->first()->url }}" alt="{{ $match['item']->title }}" class="h-12 w-12 rounded-lg object-cover ml-4">
                                            @endif
                                        </div>
                                        <div class="mt-2 flex items-center justify-between">
                                            <div class="flex items-center text-xs text-gray-500">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $match['item']->category->color ?? 'bg-gray-100' }}">
                                                    {{ $match['item']->category->name }}
                                                </span>
                                                <span class="ml-2">Found {{ $match['item']->date_found->format('M d, Y') }}</span>
                                                <span class="ml-2 text-blue-600">by {{ $match['finder'] }}</span>
                                            </div>
                                            <button wire:click="showMatchDetails({{ $match['item']->id }})" class="inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded text-blue-700 bg-blue-100 hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                                View Details
                                            </button>
                                        </div>
                                    </div>
                                </li>
                            @empty
                                <li class="px-4 py-8 text-center text-gray-500">
                                    @if($loadingMatches)
                                        Searching for matches...
                                    @else
                                        No matches found yet.
                                    @endif
                                </li>
                            @endforelse
                        </ul>
                    @else
                        <div class="px-4 py-8 text-center text-gray-500">
                            Select a lost item to find matches.
                        </div>
                    @endif
                </div>
            </div>
        @else
            <!-- Found Matches View -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-4 py-5 border-b border-gray-200 sm:px-6">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">
                            Found Matches
                        </h3>
                        @if($foundMatches->isNotEmpty())
                            <button wire:click="viewAllMatchesAnalysis" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                                View All Match Analysis
                            </button>
                        @endif
                    </div>
                </div>
                <ul class="divide-y divide-gray-200">
                    @forelse($foundMatches as $match)
                        <li class="p-4 hover:bg-gray-50">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Lost Item -->
                                <div class="border-r border-gray-200 pr-4">
                                    <div class="flex items-center justify-between">
                                        <div class="flex-1 min-w-0">
                                            <h4 class="text-sm font-medium text-gray-900">Your Lost Item</h4>
                                            <p class="text-sm text-blue-600 truncate">{{ $match['lost_item']->title }}</p>
                                        </div>
                                        <button wire:click="viewMatchAnalysis({{ $match['lost_item']->id }})" class="ml-2 inline-flex items-center px-2 py-1 border border-transparent text-xs font-medium rounded text-blue-700 bg-blue-100 hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                            </svg>
                                            Analysis
                                        </button>
                                        @if($match['lost_item']->images->isNotEmpty())
                                            <img src="{{ $match['lost_item']->images->first()->url }}" alt="{{ $match['lost_item']->title }}" class="h-12 w-12 rounded-lg object-cover ml-4">
                                        @endif
                                    </div>
                                    <p class="mt-1 text-sm text-gray-500 line-clamp-2">{{ $match['lost_item']->description }}</p>
                                    <div class="mt-2 flex items-center text-xs text-gray-500">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $match['lost_item']->category->color ?? 'bg-gray-100' }}">
                                            {{ $match['lost_item']->category->name }}
                                        </span>
                                        <span class="ml-2">Lost on {{ $match['lost_item']->date_lost->format('M d, Y') }}</span>
                                    </div>
                                </div>

                                <!-- Found Item -->
                                <div class="pl-4">
                                    <div class="flex items-center">
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center justify-between">
                                                <h4 class="text-sm font-medium text-gray-900">Found Match</h4>
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $match['similarity'] >= 0.7 ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                                    {{ number_format($match['similarity'] * 100, 0) }}% Match
                                                </span>
                                            </div>
                                            <p class="text-sm text-blue-600 truncate">{{ $match['found_item']->title }}</p>
                                        </div>
                                        @if($match['found_item']->images->isNotEmpty())
                                            <img src="{{ $match['found_item']->images->first()->url }}" alt="{{ $match['found_item']->title }}" class="h-12 w-12 rounded-lg object-cover ml-4">
                                        @endif
                                    </div>
                                    <p class="mt-1 text-sm text-gray-500 line-clamp-2">{{ $match['found_item']->description }}</p>
                                    <div class="mt-2 flex items-center justify-between">
                                        <div class="flex items-center text-xs text-gray-500">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $match['found_item']->category->color ?? 'bg-gray-100' }}">
                                                {{ $match['found_item']->category->name }}
                                            </span>
                                            <span class="ml-2">Found on {{ $match['found_item']->date_found->format('M d, Y') }}</span>
                                            <span class="ml-2 text-blue-600">by {{ $match['finder'] }}</span>
                                        </div>
                                        <button wire:click="showMatchDetails({{ $match['found_item']->id }})" class="inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded text-blue-700 bg-blue-100 hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                            View Details
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </li>
                    @empty
                        <li class="px-4 py-8 text-center text-gray-500">
                            No matches found yet.
                        </li>
                    @endforelse
                </ul>
            </div>
        @endif
    </div>

    <!-- Match Details Modal -->
    <div x-data="{ open: false }"
         x-show="open"
         x-on:open-modal.window="if ($event.detail === 'match-details') open = true"
         x-on:close-modal.window="open = false"
         x-on:keydown.escape.window="open = false"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto"
         style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>

            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                @if($matchDetailsItem)
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            @if($matchDetailsItem->images->isNotEmpty())
                                <div class="mt-3 sm:mt-0 sm:ml-4">
                                    <img src="{{ $matchDetailsItem->images->first()->url }}" alt="{{ $matchDetailsItem->title }}" class="w-full h-48 object-cover rounded-lg">
                                </div>
                            @endif
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                <h3 class="text-lg leading-6 font-medium text-gray-900">
                                    {{ $matchDetailsItem->title }}
                                </h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500">
                                        {{ $matchDetailsItem->description }}
                                    </p>
                                    <div class="mt-4 space-y-2">
                                        <p class="text-sm text-gray-500">
                                            <span class="font-medium">Category:</span> {{ $matchDetailsItem->category->name }}
                                        </p>
                                        <p class="text-sm text-gray-500">
                                            <span class="font-medium">Location:</span> {{ $matchDetailsItem->location }}
                                        </p>
                                        <p class="text-sm text-gray-500">
                                            <span class="font-medium">Date Found:</span> {{ $matchDetailsItem->date_found->format('M d, Y') }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm" x-on:click="open = false">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Match Analysis Modal -->
    <div x-data="{ open: false }"
         x-show="open"
         x-on:open-modal.window="if ($event.detail === 'match-analysis') open = true"
         x-on:close-modal.window="open = false"
         x-on:keydown.escape.window="open = false"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
         class="fixed inset-0 z-50 overflow-y-auto"
         style="display: none;">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full sm:p-6">
                @if($currentAnalysisItem)
                    <div class="absolute top-0 right-0 pt-4 pr-4">
                        <button wire:click="closeMatchAnalysis" class="bg-white rounded-md text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <span class="sr-only">Close</span>
                            <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                                Match Analysis for: {{ $currentAnalysisItem->title }}
                            </h3>

                            <!-- Your Item Details -->
                            <div class="bg-gray-50 rounded-lg p-4 mb-6">
                                <h4 class="text-sm font-medium text-gray-900 mb-2">Your Item Details</h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="space-y-2">
                                        <p class="text-sm text-gray-600"><span class="font-medium">Category:</span> {{ $currentAnalysisItem->category->name }}</p>
                                        <p class="text-sm text-gray-600"><span class="font-medium">Location:</span> {{ $currentAnalysisItem->location }}</p>
                                        <p class="text-sm text-gray-600"><span class="font-medium">Date:</span> {{ $currentAnalysisItem->date_lost->format('M d, Y') }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600"><span class="font-medium">Description:</span></p>
                                        <p class="text-sm text-gray-500 mt-1">{{ $currentAnalysisItem->description }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Match Analysis -->
                            <div class="space-y-6">
                                @foreach($matchAnalysis as $analysis)
                                    <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
                                        <div class="px-4 py-5 sm:p-6">
                                            <div class="flex items-center justify-between mb-4">
                                                <h4 class="text-lg font-medium text-gray-900">
                                                    Match #{{ $loop->iteration }}
                                                </h4>
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $analysis['similarity'] >= 0.7 ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                                    {{ number_format($analysis['similarity'] * 100, 0) }}% Overall Match
                                                </span>
                                            </div>

                                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                                <!-- Found Item Details -->
                                                <div>
                                                    <h5 class="text-sm font-medium text-gray-900 mb-2">Found Item Details</h5>
                                                    <div class="space-y-2">
                                                        <p class="text-sm text-gray-600">
                                                            <span class="font-medium">Title:</span> {{ $analysis['found_item']->title }}
                                                        </p>
                                                        <p class="text-sm text-gray-600">
                                                            <span class="font-medium">Category:</span> {{ $analysis['found_item']->category->name }}
                                                        </p>
                                                        <p class="text-sm text-gray-600">
                                                            <span class="font-medium">Location:</span> {{ $analysis['found_item']->location }}
                                                        </p>
                                                        <p class="text-sm text-gray-600">
                                                            <span class="font-medium">Found by:</span> {{ $analysis['finder'] }}
                                                        </p>
                                                        <p class="text-sm text-gray-600">
                                                            <span class="font-medium">Found on:</span> {{ $analysis['found_item']->date_found->format('M d, Y') }}
                                                        </p>
                                                        <p class="text-sm text-gray-600">
                                                            <span class="font-medium">Description:</span>
                                                            <span class="block mt-1 text-gray-500">{{ $analysis['found_item']->description }}</span>
                                                        </p>
                                                    </div>
                                                </div>

                                                <!-- Matching Scores -->
                                                <div>
                                                    <h5 class="text-sm font-medium text-gray-900 mb-4">Attribute Match Scores</h5>
                                                    <div class="space-y-4">
                                                        @foreach($analysis['matching_attributes'] as $attribute => $score)
                                                            <div>
                                                                <div class="flex items-center justify-between mb-1">
                                                                    <span class="text-sm font-medium text-gray-700">{{ ucfirst($attribute) }}</span>
                                                                    <span class="text-sm font-medium text-gray-900">{{ number_format($score, 0) }}%</span>
                                                                </div>
                                                                <div class="overflow-hidden h-2 text-xs flex rounded bg-gray-100">
                                                                    <div style="width: {{ $score }}%" class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center {{ $score >= 70 ? 'bg-green-500' : ($score >= 50 ? 'bg-yellow-500' : 'bg-red-500') }}"></div>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>

                                            @if($analysis['found_item']->images->isNotEmpty())
                                                <div class="mt-6">
                                                    <h5 class="text-sm font-medium text-gray-900 mb-2">Images</h5>
                                                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                                                        @foreach($analysis['found_item']->images as $image)
                                                            <img src="{{ $image->url }}" alt="Found item image" class="h-32 w-full object-cover rounded-lg">
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Notification -->
    <div x-data="{ show: false, message: '', type: 'success' }"
         x-on:notify.window="show = true; message = $event.detail.message; type = $event.detail.type; setTimeout(() => { show = false }, 3000)"
         x-show="show"
         x-transition:enter="transform ease-out duration-300 transition"
         x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
         x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed bottom-0 right-0 flex items-end justify-center px-4 py-6 pointer-events-none sm:p-6 sm:items-start sm:justify-end">
        <div class="max-w-sm w-full bg-white shadow-lg rounded-lg pointer-events-auto ring-1 ring-black ring-opacity-5 overflow-hidden">
            <div class="p-4">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <template x-if="type === 'success'">
                            <svg class="h-6 w-6 text-green-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </template>
                        <template x-if="type === 'error'">
                            <svg class="h-6 w-6 text-red-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </template>
                    </div>
                    <div class="ml-3 w-0 flex-1 pt-0.5">
                        <p x-text="message" class="text-sm font-medium text-gray-900"></p>
                    </div>
                    <div class="ml-4 flex-shrink-0 flex">
                        <button x-on:click="show = false" class="bg-white rounded-md inline-flex text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <span class="sr-only">Close</span>
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
