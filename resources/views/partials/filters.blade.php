<div class="p-4 sm:p-6 space-y-4">
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <!-- Search -->
        <div>
            <label for="search" class="block text-sm font-medium text-gray-700">Search</label>
            <div class="mt-1 relative rounded-md shadow-sm">
                <input type="text"
                       wire:model.live.debounce.300ms="searchQuery"
                       class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-3 pr-10 py-2 sm:text-sm border-gray-300 rounded-md"
                       placeholder="Search items...">
                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Category Filter -->
        <div>
            <label for="category" class="block text-sm font-medium text-gray-700">Category</label>
            <select wire:model.live="selectedCategory"
                    class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                <option value="">All Categories</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
        </div>

        <!-- Date Range -->
        <div>
            <label for="date-range" class="block text-sm font-medium text-gray-700">Date Range</label>
            <select wire:model.live="dateRange"
                    class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                <option value="">All Time</option>
                <option value="7">Last 7 days</option>
                <option value="30">Last 30 days</option>
                <option value="90">Last 3 months</option>
                <option value="custom">Custom Range</option>
            </select>
        </div>

        <!-- Sort By -->
        <div>
            <label for="sort" class="block text-sm font-medium text-gray-700">Sort By</label>
            <select wire:model.live="sortBy"
                    class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                <option value="latest">Latest</option>
                <option value="oldest">Oldest</option>
                <option value="highest_value">Highest Value</option>
                <option value="most_similar">Most Similar</option>
                <option value="location">Nearest Location</option>
            </select>
        </div>
    </div>

    <!-- Advanced Filters -->
    <div x-data="{ showAdvanced: false }" class="pt-4">
        <button @click="showAdvanced = !showAdvanced"
                type="button"
                class="flex items-center text-sm text-gray-600 hover:text-gray-900">
            <svg class="h-5 w-5 mr-2"
                 :class="{ 'transform rotate-90': showAdvanced }"
                 xmlns="http://www.w3.org/2000/svg"
                 fill="none"
                 viewBox="0 0 24 24"
                 stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
            Advanced Filters
        </button>

        <div x-show="showAdvanced"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 transform -translate-y-2"
             x-transition:enter-end="opacity-100 transform translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 transform translate-y-0"
             x-transition:leave-end="opacity-0 transform -translate-y-2"
             class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">

            <!-- Location Range -->
            <div>
                <label for="location-range" class="block text-sm font-medium text-gray-700">Location Range</label>
                <select wire:model.live="locationRange"
                        class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                    <option value="">Any Distance</option>
                    <option value="1">Within 1 km</option>
                    <option value="5">Within 5 km</option>
                    <option value="10">Within 10 km</option>
                    <option value="25">Within 25 km</option>
                    <option value="50">Within 50 km</option>
                </select>
            </div>

            <!-- Has Images Only -->
            <div class="flex items-center">
                <input type="checkbox"
                       wire:model.live="filters.has_images"
                       class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                <label for="has-images" class="ml-2 block text-sm text-gray-700">
                    Items with Images Only
                </label>
            </div>

            <!-- Minimum Similarity -->
            <div>
                <label for="min-similarity" class="block text-sm font-medium text-gray-700">
                    Minimum Similarity
                    <span class="text-gray-500 text-xs" x-text="`${$wire.filters.min_similarity * 100}%`"></span>
                </label>
                <input type="range"
                       wire:model.live="filters.min_similarity"
                       min="0.4"
                       max="0.9"
                       step="0.1"
                       class="mt-1 w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer">
            </div>

            <!-- Status -->
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                <select wire:model.live="filters.status"
                        class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                    <option value="">All Statuses</option>
                    <option value="active">Active</option>
                    <option value="pending">Pending</option>
                    <option value="resolved">Resolved</option>
                    <option value="expired">Expired</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Active Filters -->
    <div x-data="{ activeFilters: @entangle('activeFilters') }" class="flex flex-wrap gap-2">
        <template x-for="filter in activeFilters" :key="filter.id">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-indigo-100 text-indigo-800">
                <span x-text="filter.label"></span>
                <button type="button" @click="$wire.removeFilter(filter.id)" class="ml-2 flex-shrink-0 inline-flex text-indigo-600 hover:text-indigo-900">
                    <span class="sr-only">Remove filter</span>
                    <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </span>
        </template>
    </div>
</div>
