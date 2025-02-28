<div class="min-h-screen bg-gray-100" x-data="{ isFilterOpen: true }">
    <!-- Filter Toggle Button for Mobile -->
    <div class="md:hidden p-4 bg-white shadow-md">
        <button @click="isFilterOpen = !isFilterOpen" class="flex items-center space-x-2">
            <span class="text-gray-700">Filters</span>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path>
            </svg>
        </button>
    </div>

    <!-- Main Grid -->
    <div class="container mx-auto px-4 py-6 flex flex-col md:flex-row space-y-6 md:space-y-0 md:space-x-6">
        <!-- Filters Section (Left) -->
        <div class="w-full md:w-64 lg:w-80" :class="{ 'hidden md:block': !isFilterOpen }">
            <div class="bg-white rounded-lg shadow-lg p-6 sticky top-6">
                <h2 class="text-xl font-semibold mb-6 text-gray-800">Filters</h2>

                <!-- Search Input -->
                <div class="space-y-4">
                    <div class="relative">
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                        <div class="relative rounded-md shadow-sm">
                            <input type="text"
                                id="search"
                                wire:model.debounce.300ms="search"
                                class="block w-full rounded-md border-gray-300 pl-10 focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                placeholder="Search items...">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Category Filter -->
                    <div>
                        <label for="category" class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                        <select id="category"
                            wire:model="category"
                            class="block w-full rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            <option value="">All Categories</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Date Filter -->
                    <div>
                        <label for="date" class="block text-sm font-medium text-gray-700 mb-1">Date Lost</label>
                        <input type="date"
                            id="date"
                            wire:model="date_lost"
                            class="block w-full rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    </div>

                    <!-- View Filter -->
                    <div>
                        <label for="filter" class="block text-sm font-medium text-gray-700 mb-1">View</label>
                        <select id="filter"
                            wire:model="filter"
                            class="block w-full rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            <option value="all">All Items</option>
                            <option value="mine">My Items</option>
                            <option value="others">Others' Items</option>
                        </select>
                    </div>
                </div>

                <!-- Active Filters -->
                @if($search || $category || $location || $date_lost || $condition)
                    <div class="mt-6 pt-4 border-t border-gray-200">
                        <h3 class="text-sm font-medium text-gray-700 mb-3">Active Filters</h3>
                        <div class="flex flex-wrap gap-2">
                            <button wire:click="clearAllFilters"
                                class="inline-flex items-center px-2.5 py-1.5 text-xs font-medium text-red-700 bg-red-100 rounded-full hover:bg-red-200 transition duration-150 ease-in-out">
                                Clear All Filters
                                <svg class="ml-1.5 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                            @if($search)
                                <span class="inline-flex items-center px-2.5 py-1.5 text-xs font-medium text-blue-700 bg-blue-100 rounded-full">
                                    Search: {{ $search }}
                                    <button wire:click="clearFilter('search')" class="ml-1 text-blue-500 hover:text-blue-700">×</button>
                                </span>
                            @endif
                            @if($category)
                                <span class="inline-flex items-center px-2.5 py-1.5 text-xs font-medium text-green-700 bg-green-100 rounded-full">
                                    Category: {{ $categories->find($category)->name }}
                                    <button wire:click="clearFilter('category')" class="ml-1 text-green-500 hover:text-green-700">×</button>
                                </span>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Lost Items Section (Right) -->
        <div class="flex-1">
            <!-- Lost Items Grid -->
            @if ($lostItems->isEmpty())
                <div class="text-center text-gray-600 py-8">
                    No results found for
                    @if ($search)
                        search term "{{ $search }}"
                    @endif
                    @if ($category)
                        in category "{{ $categories->find($category)->name }}"
                    @endif
                    @if ($location)
                        at location "{{ $location }}"
                    @endif
                    @if ($date_lost)
                        on date "{{ $date_lost }}"
                    @endif
                    @if ($condition)
                        with condition "{{ $condition }}"
                    @endif
                </div>
            @else
                @if ($selectedItem)
                    <!-- Detailed View -->
                    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
                        <div class="bg-white rounded-lg shadow-lg w-full max-w-4xl p-6 overflow-y-auto max-h-[90vh]">
                            <!-- Sticky Header -->
                            <div class="sticky top-0 bg-white py-4 z-10 border-b border-gray-200">
                                <div class="flex justify-between items-center">
                                    <h2 class="text-2xl font-bold text-gray-900">{{ $selectedItem->title }}</h2>
                                    <div class="flex items-center space-x-4">
                                        <!-- Dropdown Menu Trigger -->
                                        <x-dropdown-menu>
                                            <x-slot name="trigger">
                                                <button
                                                    class="p-2 text-gray-500 hover:text-gray-700 transition-colors duration-200">
                                                    <svg class="w-6 h-6" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path>
                                                    </svg>
                                                </button>
                                            </x-slot>
                                            <x-slot name="items">
                                                <button wire:click="confirmDownload('pdf')"
                                                    class="block w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                    Download PDF
                                                </button>
                                                <button wire:click="confirmDownload('qr')"
                                                    class="block w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                    Download QR Code (JPG)
                                                </button>
                                            </x-slot>
                                        </x-dropdown-menu>
                                        <!-- Close Button -->
                                        <button wire:click="closeItemDetails"
                                            class="text-gray-500 hover:text-gray-700 transition-colors duration-200">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Content -->
                            <div class="mt-6">
                                <!-- Images Collage -->
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
                                    @foreach ($selectedItem->images as $index => $image)
                                        <div x-data="{ zoomed: false }" @mouseenter="zoomed = true"
                                            @mouseleave="zoomed = false"
                                            class="relative overflow-hidden rounded-lg transition-transform duration-300"
                                            :class="{ 'scale-105': zoomed }"
                                            :style="'aspect-ratio: {{ $index === 0 ? '2 / 1' : '1 / 1' }}'">
                                            <img src="{{ asset('storage/' . $image->image_path) }}"
                                                alt="{{ $selectedItem->title }}"
                                                class="absolute inset-0 w-full h-full object-cover" loading="lazy">
                                        </div>
                                    @endforeach
                                </div>

                                <!-- Details Section -->
                                <div class="space-y-8">
                                    <!-- Description (Bold Paragraph) -->
                                    <div>
                                        <h3 class="text-xl font-semibold text-gray-900 mb-3">About the Item</h3>
                                        <p class="text-gray-600 leading-relaxed">
                                            {{ $selectedItem->description }}
                                        </p>
                                    </div>

                                    <!-- Conditional Sections Based on Item Type -->
                                    @if ($selectedItem->item_type === 'reported' || $selectedItem->item_type === 'searched')
                                        <!-- Emotional Context for Reported/Searched Items -->
                                        <div>
                                            <h3 class="text-xl font-semibold text-gray-900 mb-3">Last Seen</h3>
                                            <p class="text-gray-600 leading-relaxed">
                                                This item holds significant value to its owner. It was last seen at
                                                <span class="font-medium">{{ $selectedItem->location }}</span> on
                                                <span
                                                    class="font-medium">{{ $selectedItem->date_lost ? \Carbon\Carbon::parse($date_lost)->format('F j, Y') : 'Not provided' }}</span>.
                                                The owner is deeply concerned and hopes for its safe return.
                                            </p>
                                        </div>

                                        <!-- Additional Details for Reported/Searched Items -->
                                        <div>
                                            <h3 class="text-xl font-semibold text-gray-900 mb-3">Reported By</h3>
                                            <p class="text-gray-600 leading-relaxed">
                                                The item was in <span
                                                    class="font-medium">{{ $selectedItem->condition }}</span>
                                                condition
                                                when it was lost. It was reported by <span class="font-medium">
                                                    @if ($selectedItem->is_anonymous)
                                                        an anonymous individual
                                                    @else
                                                        {{ $selectedItem->user->name }}
                                                    @endif
                                                </span>, who is eagerly awaiting any information that could help recover
                                                it.
                                            </p>
                                        </div>
                                    @elseif ($selectedItem->item_type === 'found')
                                        <!-- Found Item Details -->
                                        <div>
                                            <h3 class="text-xl font-semibold text-gray-900 mb-3">Found Details</h3>
                                            <p class="text-gray-600 leading-relaxed">
                                                This item was found at <span
                                                    class="font-medium">{{ $selectedItem->location }}</span> on
                                                <span
                                                    class="font-medium">{{ $selectedItem->date_found ? \Carbon\Carbon::parse($date_lost)->format('F j, Y') : 'Not provided' }}</span>.
                                                It is currently in <span
                                                    class="font-medium">{{ $selectedItem->condition }}</span>
                                                condition.
                                            </p>
                                        </div>

                                        <!-- Found By Information -->
                                        <div>
                                            <h3 class="text-xl font-semibold text-gray-900 mb-3">Found By</h3>
                                            <p class="text-gray-600 leading-relaxed">
                                                The item was found by <span
                                                    class="font-medium">{{ $selectedItem->user->name ?? 'Unknown' }}</span>.
                                                If this item belongs to you, please contact the finder or the platform
                                                administrator to
                                                claim it.
                                            </p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Preview Modal -->
                    <x-dialog-modal wire:model.live="previewContent">
                        <x-slot name="title">
                            Preview
                        </x-slot>
                        <x-slot name="content">
                            <div class="flex justify-center">
                                {!! $previewContent !!}
                            </div>
                        </x-slot>
                        <x-slot name="footer">
                            <x-secondary-button wire:click="$set('previewContent', '')">
                                Close
                            </x-secondary-button>
                        </x-slot>
                    </x-dialog-modal>

                    <!-- Download Confirmation Modal -->
                    <x-dialog-modal wire:model.live="confirmingDownload">
                        <x-slot name="title">
                            Confirm Download
                        </x-slot>
                        <x-slot name="content">
                            Are you sure you want to download this {{ $downloadType }}?
                        </x-slot>
                        <x-slot name="footer">
                            <x-secondary-button wire:click="$set('confirmingDownload', false)">
                                Cancel
                            </x-secondary-button>
                            <x-button wire:click="downloadItem">
                                Download
                            </x-button>
                        </x-slot>
                    </x-dialog-modal>
                @endif
                <div>
                   <div wire:poll.5s>
                        <!-- Section for All Items -->
                        <div wire:ignore class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 p-6">
                            @foreach ($lostItems as $item)
                                <div class="rounded-xl shadow-lg overflow-hidden transition-all duration-500 ease-in-out transform hover:scale-105 hover:shadow-2xl relative"
                                    style="background: linear-gradient(145deg, {{ $item->claimed_by ? '#f0f0f0' : ($item->user_id === Auth::id() ? '#e0f7fa' : '#ffffff') }}, {{ $item->claimed_by ? '#e0e0e0' : ($item->user_id === Auth::id() ? '#b2ebf2' : '#f5f5f5') }});">

                                    <!-- Header Section for Badges -->
                                    <div class="p-4 bg-white bg-opacity-90 flex justify-between items-start">
                                        <!-- Item Type Badge -->
                                        <div class="flex items-center space-x-2">
                                            @php
                                                $itemTypeColor =
                                                    [
                                                        'reported' => 'bg-purple-500 text-white',
                                                        'searched' => 'bg-yellow-500 text-white',
                                                        'found' => 'bg-green-500 text-white',
                                                    ][$item->item_type] ?? 'bg-gray-500 text-white';
                                            @endphp
                                            <span
                                                class="{{ $itemTypeColor }} px-3 py-1 rounded-full text-xs font-semibold shadow-md">
                                                {{ ucfirst($item->item_type) }}
                                            </span>

                                            <!-- Image Matching Percentage (Displayed only for "Found" items) -->
                                            @if ($item->item_type === 'found' && Auth::check())
                                                @php
                                                    $userReportedItems = \App\Models\LostItem::where('user_id', Auth::id())
                                                        ->whereIn('item_type', ['reported', 'searched'])
                                                        ->with('images')
                                                        ->get();

                                                    $imageSimilarityScore = null;
                                                    if ($userReportedItems->isNotEmpty()) {
                                                        $imageSimilarityScore = $this->calculateImageSimilarity(
                                                            $userReportedItems->first(),
                                                            $item
                                                        );
                                                    }
                                                @endphp
                                                @if ($imageSimilarityScore !== null)
                                                    <div class="relative group">
                                                        <span class="bg-blue-500 text-white px-3 py-1 rounded-full text-xs font-semibold shadow-md">
                                                            {{ number_format($imageSimilarityScore * 100, 2) }}%
                                                        </span>
                                                    </div>
                                                @endif
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Image Section -->
                                    <div class="relative w-full h-48 overflow-hidden">
                                        @if ($item->images->isNotEmpty())
                                            <img src="{{ asset('storage/' . $item->images->first()->image_path) }}"
                                                alt="{{ $item->title }}"
                                                class="w-full h-full object-cover transition-transform duration-500 ease-in-out hover:scale-110">
                                        @else
                                            <div class="w-full h-full bg-gray-100 flex items-center justify-center">
                                                <span class="text-gray-500 text-xs">No Image Available</span>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Content Section -->
                                    <div class="p-4">
                                        <!-- Title with Eye Icon -->
                                        <div class="flex justify-between items-center mb-3">
                                            <h3 class="text-xl font-bold text-gray-900">{{ $item->title }}</h3>
                                            <button wire:click="showItemDetails({{ $item->id }})"
                                                class="text-gray-500 hover:text-blue-600 transition-colors duration-500 ease-in-out"
                                                data-tippy-content="View Item Details">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>

                                        <!-- Reported By -->
                                        <div class="flex items-center space-x-2 text-sm text-gray-600 mb-3">
                                            <i class="fas fa-user"></i>
                                            <span class="text-gray-700">{{ $item->user->name }}</span>
                                        </div>

                                        <!-- Location Badge -->
                                        <div class="flex items-center space-x-2 mb-3">
                                            <i class="fas fa-map-marker-alt text-blue-500 animate-pulse"></i>
                                            <span
                                                class="text-sm text-gray-700 bg-blue-50 px-3 py-1 rounded-full">{{ $item->location }}</span>
                                        </div>

                                        <!-- Matched Item Information (For Reported and Searched Items) -->
                                        @if (in_array($item->item_type, ['reported', 'searched']) && $item->matchedFoundItem)
                                            <div class="mt-4 p-3 bg-gray-50 rounded-lg">
                                                <h4 class="text-sm font-semibold text-gray-700 mb-2">Matched Found Item
                                                </h4>
                                                <div class="space-y-2">
                                                    <div class="flex items-center space-x-2">
                                                        <i class="fas fa-check-circle text-green-500"></i>
                                                        <span class="text-sm text-gray-700">
                                                            Found by: {{ $item->matchedFoundItem->user->name }}
                                                        </span>
                                                    </div>
                                                    <div class="flex items-center space-x-2">
                                                        <i class="fas fa-map-marker-alt text-blue-500"></i>
                                                        <span class="text-sm text-gray-700">
                                                            Found at: {{ $item->matchedFoundItem->location }}
                                                        </span>
                                                    </div>
                                                    <div class="flex items-center space-x-2">
                                                        <i class="fas fa-calendar-alt text-gray-500"></i>
                                                        <span class="text-sm text-gray-700">
                                                            Found on: {{ $item->matchedFoundItem->date_found->format('M d, Y') }}
                                                        </span>
                                                    </div>
                                                    @if ($item->matchedFoundItem->claimed_by)
                                                        <div class="flex items-center space-x-2">
                                                            <i class="fas fa-user-check text-purple-500"></i>
                                                            <span class="text-sm text-gray-700">
                                                                Claimed by:
                                                                {{ $item->matchedFoundItem->claimedByUser->name }}
                                                            </span>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endif

                                        <!-- Claim Button (Centered at the Bottom) -->
                                        @if ($item->item_type === 'found' && $item->user_id !== Auth::id() && !$item->claimed_by && $imageSimilarityScore > 0.5)
                                            <div
                                                class="absolute bottom-0 left-0 right-0 flex justify-center p-3 bg-white bg-opacity-90">
                                                <button wire:click="confirmClaim({{ $item->id }})"
                                                    class="bg-green-500 text-white hover:bg-green-600 transition-colors duration-500 ease-in-out rounded-full px-6 py-2 text-sm shadow-sm hover:shadow-md flex items-center space-x-2"
                                                    data-tippy-content="Claim Item">
                                                    <i class="fas fa-hand-holding-heart"></i>
                                                    <span>Claim</span>
                                                </button>
                                            </div>
                                        @endif

                                        <!-- Claimed Message -->
                                        @if ($item->claimed_by)
                                            <div
                                                class="absolute bottom-0 left-0 right-0 flex justify-center p-3 bg-gray-200 bg-opacity-90">
                                                <span class="text-sm text-gray-700 font-semibold">
                                                    Found and Claimed by {{ $item->claimedByUser->name }}
                                                </span>
                                            </div>
                                        @endif

                                        <!-- Reset Claim Button (For Admins/Superadmins) -->
                                        @if ($item->claimed_by && (auth()->user()->hasRole('admin') || auth()->user()->hasRole('superadmin')))
                                            <div
                                                class="absolute bottom-0 left-0 right-0 flex justify-center p-3 bg-white bg-opacity-90">
                                                <button wire:click="confirmResetClaim({{ $item->id }})"
                                                    class="bg-red-500 text-white hover:bg-red-600 transition-colors duration-500 ease-in-out rounded-full px-6 py-2 text-sm shadow-sm hover:shadow-md flex items-center space-x-2"
                                                    data-tippy-content="Reset Claim">
                                                    <i class="fas fa-undo"></i>
                                                    <span>Reset Claim</span>
                                                </button>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>


                    <!-- Pagination -->
                    <div class="mt-6">
                        {{ $lostItems->links() }}
                    </div>
                </div>
            @endif
        </div>
        <!-- Delete Confirmation Modal -->
        <x-dialog-modal wire:model.live="confirmingDelete">
            <x-slot name="title">
                {{ __('Delete Item') }}
            </x-slot>

            <x-slot name="content">
                {{ __('Are you sure you want to delete this item? This action cannot be undone.') }}
            </x-slot>

            <x-slot name="footer">
                <x-secondary-button wire:click="$toggle('confirmingDelete')" wire:loading.attr="disabled">
                    {{ __('Cancel') }}
                </x-secondary-button>

                <x-button class="ms-3 bg-red-600 hover:bg-red-700" wire:click="deleteItem"
                    wire:loading.attr="disabled">
                    {{ __('Delete') }}
                </x-button>
            </x-slot>
        </x-dialog-modal>

       <!-- Claim Confirmation Modal -->
<x-dialog-modal wire:model.live="confirmingClaim">
    <x-slot name="title">
        {{ __('Claim Item') }}
    </x-slot>

    <x-slot name="content">
        <div class="space-y-6">
            <!-- Confirmation Message -->
            <p class="text-gray-700 text-sm leading-relaxed">
                {{ __('Are you sure you want to claim this item? Please review the following checks to ensure it matches your lost item.') }}
            </p>

            <!-- Similarity Scores Section -->
            <div class="space-y-4">
                <!-- Text Similarity Score -->
                <div class="flex items-center justify-between">
                    <span class="text-gray-700 font-medium">Description Match:</span>
                    <div class="flex items-center space-x-2">
                        <span class="text-sm font-semibold text-gray-700">
                            {{ number_format($textSimilarityScore * 100, 2) }}%
                        </span>
                        <div class="w-24 h-2 bg-gray-200 rounded-full overflow-hidden">
                            <div class="h-full bg-blue-500 rounded-full"
                                style="width: {{ $textSimilarityScore * 100 }}%;"></div>
                        </div>
                    </div>
                </div>

                <!-- Image Similarity Score -->
                <div class="flex items-center justify-between">
                    <span class="text-gray-700 font-medium">Image Match:</span>
                    <div class="flex items-center space-x-2">
                        <span class="text-sm font-semibold text-gray-700">
                            {{ number_format($imageSimilarityScore * 100, 2) }}%
                        </span>
                        <div class="w-24 h-2 bg-gray-200 rounded-full overflow-hidden">
                            <div class="h-full bg-green-500 rounded-full"
                                style="width: {{ $imageSimilarityScore * 100 }}%;"></div>
                        </div>
                    </div>
                </div>

                <!-- Location Similarity Score -->
                <div class="flex items-center justify-between">
                    <span class="text-gray-700 font-medium">Location Match:</span>
                    <div class="flex items-center space-x-2">
                        <span class="text-sm font-semibold text-gray-700">
                            {{ number_format($locationSimilarityScore * 100, 2) }}%
                        </span>
                        <div class="w-24 h-2 bg-gray-200 rounded-full overflow-hidden">
                            <div class="h-full bg-purple-500 rounded-full"
                                style="width: {{ $locationSimilarityScore * 100 }}%;"></div>
                        </div>
                    </div>
                </div>

                <!-- Time Similarity Score -->
                <div class="flex items-center justify-between">
                    <span class="text-gray-700 font-medium">Time Match:</span>
                    <div class="flex items-center space-x-2">
                        <span class="text-sm font-semibold text-gray-700">
                            {{ number_format($timeSimilarityScore * 100, 2) }}%
                        </span>
                        <div class="w-24 h-2 bg-gray-200 rounded-full overflow-hidden">
                            <div class="h-full bg-yellow-500 rounded-full"
                                style="width: {{ $timeSimilarityScore * 100 }}%;"></div>
                        </div>
                    </div>
                </div>


            </div>

            <!-- Total Similarity Score Display -->
            <div class="text-center">
                <span class="text-2xl font-bold text-gray-800">
                    Total Similarity Score: {{ number_format($totalSimilarityScore * 100, 2) }}%
                </span>
            </div>

            <!-- Checks List -->
            <div class="space-y-3">
                <!-- Description Matches -->
                <div class="flex items-center space-x-3">
                    <div class="flex-shrink-0">
                        @if ($checks['description_matches'] ?? false)
                            <i class="fas fa-check-circle text-green-500"></i>
                        @else
                            <i class="fas fa-times-circle text-red-500"></i>
                        @endif
                    </div>
                    <span class="text-gray-700 text-sm">
                        The description matches the item you were looking for.
                    </span>
                </div>

                <!-- Images Match -->
                <div class="flex items-center space-x-3">
                    <div class="flex-shrink-0">
                        @if ($checks['images_match'] ?? false)
                            <i class="fas fa-check-circle text-green-500"></i>
                        @else
                            <i class="fas fa-times-circle text-red-500"></i>
                        @endif
                    </div>
                    <span class="text-gray-700 text-sm">
                        The images match the item you were looking for.
                    </span>
                </div>

                <!-- Location Matches -->
                <div class="flex items-center space-x-3">
                    <div class="flex-shrink-0">
                        @if ($checks['location_matches'] ?? false)
                            <i class="fas fa-check-circle text-green-500"></i>
                        @else
                            <i class="fas fa-times-circle text-red-500"></i>
                        @endif
                    </div>
                    <span class="text-gray-700 text-sm">
                        The location matches where you lost the item.
                    </span>
                </div>

                <!-- Time Matches -->
                <div class="flex items-center space-x-3">
                    <div class="flex-shrink-0">
                        @if ($checks['time_matches'] ?? false)
                            <i class="fas fa-check-circle text-green-500"></i>
                        @else
                            <i class="fas fa-times-circle text-red-500"></i>
                        @endif
                    </div>
                    <span class="text-gray-700 text-sm">
                        The time matches when you lost the item.
                    </span>
                </div>
            </div>
        </div>
    </x-slot>

    <x-slot name="footer">
        <x-secondary-button wire:click="closeClaimModal" wire:loading.attr="disabled">
            {{ __('Cancel') }}
        </x-secondary-button>

        <x-button class="ms-3 bg-green-600 hover:bg-green-700" wire:click="processClaim"
            wire:loading.attr="disabled">
            {{ __('Claim') }}
        </x-button>
    </x-slot>
</x-dialog-modal>



        <!-- Reset Claim Modal -->
        <x-dialog-modal wire:model.live="confirmingResetClaim">
            <x-slot name="title">
                {{ __('Reset Claim') }}
            </x-slot>

            <x-slot name="content">
                <p class="text-gray-700">
                    {{ __('Are you sure you want to reset the claim for this item?') }}
                </p>
            </x-slot>

            <x-slot name="footer">
                <div class="flex justify-end space-x-4">
                    <x-secondary-button wire:click="closeResetClaimModal">
                        {{ __('Cancel') }}
                    </x-secondary-button>
                    <x-button wire:click="resetClaim" class="bg-red-500 hover:bg-red-600">
                        {{ __('Reset Claim') }}
                    </x-button>
                </div>
            </x-slot>
        </x-dialog-modal>

    </div>
</div>
