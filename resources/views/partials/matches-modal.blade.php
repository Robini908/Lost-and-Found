<!-- Backdrop -->
<div class="fixed inset-0 bg-gray-900 bg-opacity-75 backdrop-blur-sm transition-opacity"
     x-transition:enter="ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     aria-hidden="true"></div>

<!-- Modal Container -->
<div class="fixed inset-0 z-10 overflow-y-auto"
     x-data="{
        activeTab: 'details',
        showConfirmDialog: false,
        selectedMatchId: null,
        matchPercentage: 0,
        showImagePreview: false,
        currentImage: null
     }">
    <div class="flex min-h-screen items-center justify-center p-4 text-center sm:p-0">
        <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-4xl"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">

            <!-- Modal Header -->
            <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-xl font-semibold text-white" id="modal-title">
                            Potential Matches for "{{ $selectedItem->title }}"
                        </h3>
                    <button wire:click="closeMatches"
                            class="rounded-full p-1 text-white hover:bg-white/20 transition-colors duration-200">
                            <span class="sr-only">Close</span>
                            <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Tabs -->
                <div class="mt-4 border-b border-white/20">
                        <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                            <button @click="activeTab = 'details'"
                                :class="{ 'border-white text-white': activeTab === 'details',
                                        'border-transparent text-white/70 hover:text-white hover:border-white/50': activeTab !== 'details' }"
                                class="group flex items-center border-b-2 px-1 pb-4 text-sm font-medium transition-all duration-200">
                            <svg class="mr-2 h-5 w-5" :class="activeTab === 'details' ? 'text-white' : 'text-white/70 group-hover:text-white'" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                                Match Details
                            </button>
                            <button @click="activeTab = 'compare'"
                                :class="{ 'border-white text-white': activeTab === 'compare',
                                        'border-transparent text-white/70 hover:text-white hover:border-white/50': activeTab !== 'compare' }"
                                class="group flex items-center border-b-2 px-1 pb-4 text-sm font-medium transition-all duration-200">
                            <svg class="mr-2 h-5 w-5" :class="activeTab === 'compare' ? 'text-white' : 'text-white/70 group-hover:text-white'" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                            </svg>
                                Compare Items
                            </button>
                            <button @click="activeTab = 'map'"
                                :class="{ 'border-white text-white': activeTab === 'map',
                                        'border-transparent text-white/70 hover:text-white hover:border-white/50': activeTab !== 'map' }"
                                class="group flex items-center border-b-2 px-1 pb-4 text-sm font-medium transition-all duration-200">
                            <svg class="mr-2 h-5 w-5" :class="activeTab === 'map' ? 'text-white' : 'text-white/70 group-hover:text-white'" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                            </svg>
                                Location Map
                            </button>
                        </nav>
                </div>
                    </div>

            <!-- Modal Content -->
            <div class="px-6 py-4">
                        <!-- Loading State -->
                        @if($loadingMatches)
                            <div class="flex items-center justify-center py-12">
                        <div class="flex flex-col items-center space-y-4">
                            <div class="relative">
                                <div class="h-16 w-16 animate-spin rounded-full border-b-4 border-t-4 border-blue-600"></div>
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <div class="h-10 w-10 rounded-full bg-white"></div>
                                </div>
                            </div>
                            <div class="text-center">
                                <p class="text-sm font-medium text-gray-900">Finding matches...</p>
                                <p class="text-sm text-gray-500">This may take a moment</p>
                            </div>
                        </div>
                            </div>
                        @else
                            <!-- No Matches Found -->
                            @if(empty($selectedItemMatches))
                                <div class="text-center py-12">
                            <div class="mx-auto h-24 w-24 text-gray-400">
                                <svg class="h-full w-full" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                            </div>
                            <h3 class="mt-4 text-lg font-medium text-gray-900">No matches found</h3>
                            <p class="mt-2 text-sm text-gray-500 max-w-sm mx-auto">
                                We couldn't find any potential matches for this item. We'll keep looking and notify you if we find something.
                            </p>
                            <button wire:click="closeMatches" class="mt-6 inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                                Close and Continue
                            </button>
                                </div>
                            @else
                                <!-- Match Details Tab -->
                        <div x-show="activeTab === 'details'"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 translate-y-1"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             class="space-y-4">
                                    @foreach($selectedItemMatches as $match)
                                <div class="group relative rounded-xl border border-gray-200 bg-white shadow-sm transition-all duration-200 hover:shadow-md">
                                    <div class="p-6">
                                        <!-- Match Score Badge -->
                                        <div class="absolute right-4 top-4">
                                            <div class="flex items-center space-x-1 rounded-full {{ $match['similarity'] >= 0.8 ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }} px-3 py-1 text-sm font-semibold">
                                                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                <span>{{ number_format($match['similarity'] * 100, 1) }}% Match</span>
                                            </div>
                                        </div>

                                        <!-- Item Details -->
                                        <div class="flex items-start space-x-6">
                                            @if($match['item']->images->isNotEmpty())
                                                <div class="relative flex-shrink-0 cursor-pointer overflow-hidden rounded-lg"
                                                     @click="showImagePreview = true; currentImage = '{{ Storage::url($match['item']->images->first()->image_path) }}'">
                                                    <img src="{{ Storage::url($match['item']->images->first()->image_path) }}"
                                                         alt="{{ $match['item']->title }}"
                                                         class="h-32 w-32 object-cover transition-transform duration-200 hover:scale-110">
                                                    <div class="absolute inset-0 flex items-center justify-center bg-black bg-opacity-40 opacity-0 transition-opacity duration-200 group-hover:opacity-100">
                                                        <svg class="h-8 w-8 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7" />
                                                        </svg>
                                                    </div>
                                                </div>
                                            @endif

                                                    <div class="flex-1">
                                                        <h4 class="text-lg font-medium text-gray-900">{{ $match['item']->title }}</h4>
                                                        <p class="mt-1 text-sm text-gray-500">{{ Str::limit($match['item']->description, 150) }}</p>

                                                <!-- Item Attributes -->
                                                <dl class="mt-4 grid grid-cols-2 gap-4 sm:grid-cols-3">
                                                    <div>
                                                        <dt class="text-sm font-medium text-gray-500">Found Date</dt>
                                                        <dd class="mt-1 flex items-center text-sm text-gray-900">
                                                            <svg class="mr-1.5 h-4 w-4 flex-shrink-0 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                            </svg>
                                                            {{ $match['item']->date_found ? $match['item']->date_found->format('M d, Y') : $match['item']->created_at->format('M d, Y') }}
                                                        </dd>
                                                    </div>
                                                    <div>
                                                        <dt class="text-sm font-medium text-gray-500">Location</dt>
                                                        <dd class="mt-1 flex items-center text-sm text-gray-900">
                                                            <svg class="mr-1.5 h-4 w-4 flex-shrink-0 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                                            </svg>
                                                            {{ $match['item']->location_address ?: 'Not specified' }}
                                                        </dd>
                                                    </div>
                                                    @if($match['item']->brand)
                                                        <div>
                                                            <dt class="text-sm font-medium text-gray-500">Brand</dt>
                                                            <dd class="mt-1 flex items-center text-sm text-gray-900">
                                                                <svg class="mr-1.5 h-4 w-4 flex-shrink-0 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 4v12l-4-2-4 2V4M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                                </svg>
                                                                {{ $match['item']->brand }}
                                                            </dd>
                                                            </div>
                                                        @endif
                                                        @if($match['item']->color)
                                                        <div>
                                                                <dt class="text-sm font-medium text-gray-500">Color</dt>
                                                                <dd class="mt-1 flex items-center text-sm text-gray-900">
                                                                <span class="mr-1.5 h-4 w-4 rounded-full border border-gray-200" style="background-color: {{ $match['item']->color }}"></span>
                                                                    {{ $match['item']->color }}
                                                                </dd>
                                                            </div>
                                                        @endif
                                                    </dl>
                                            </div>
                                                </div>

                                        <!-- Action Buttons -->
                                        <div class="mt-6 flex items-center justify-end space-x-3">
                                                    <button wire:click="rejectMatch({{ $match['item']->id }})"
                                                    class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm transition-colors duration-200 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                                <svg class="mr-2 h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                        </svg>
                                                        Reject
                                                    </button>
                                                    <button @click="$dispatch('open-compare', { item1: {{ $selectedItem->id }}, item2: {{ $match['item']->id }} })"
                                                    class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm transition-colors duration-200 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                                <svg class="mr-2 h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                                                        </svg>
                                                        Compare
                                                    </button>
                                            <button wire:click="confirmMatch({{ $match['item']->id }})"
                                                    class="inline-flex items-center rounded-lg border border-transparent bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition-colors duration-200 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                                <svg class="mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                </svg>
                                                Confirm Match
                                            </button>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <!-- Compare Tab -->
                        <div x-show="activeTab === 'compare'"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 translate-y-1"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             class="space-y-6">
                            <div class="grid grid-cols-2 gap-8">
                                        <!-- Original Item -->
                                <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                                    <h4 class="mb-4 text-lg font-medium text-gray-900">Original Item</h4>
                                            @if($selectedItem->images->isNotEmpty())
                                        <div class="relative mb-4 overflow-hidden rounded-lg">
                                                <img src="{{ Storage::url($selectedItem->images->first()->image_path) }}"
                                                     alt="{{ $selectedItem->title }}"
                                                 class="h-48 w-full object-cover">
                                        </div>
                                            @endif
                                    <dl class="space-y-4">
                                                <div>
                                                    <dt class="text-sm font-medium text-gray-500">Title</dt>
                                                    <dd class="mt-1 text-sm text-gray-900">{{ $selectedItem->title }}</dd>
                                                </div>
                                                <div>
                                                    <dt class="text-sm font-medium text-gray-500">Description</dt>
                                                    <dd class="mt-1 text-sm text-gray-900">{{ $selectedItem->description }}</dd>
                                                </div>
                                        @if($selectedItem->category)
                                            <div>
                                                <dt class="text-sm font-medium text-gray-500">Category</dt>
                                                <dd class="mt-1 text-sm text-gray-900">{{ $selectedItem->category->name }}</dd>
                                            </div>
                                        @endif
                                        @if($selectedItem->brand)
                                            <div>
                                                <dt class="text-sm font-medium text-gray-500">Brand</dt>
                                                <dd class="mt-1 text-sm text-gray-900">{{ $selectedItem->brand }}</dd>
                                            </div>
                                        @endif
                                            </dl>
                                        </div>

                                        <!-- Selected Match -->
                                <div x-show="compareItems && compareItems.item2"
                                     class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                                            <template x-if="compareItems">
                                                <!-- Dynamic content will be loaded here -->
                                            </template>
                                        </div>
                                    </div>
                                </div>

                                <!-- Map Tab -->
                        <div x-show="activeTab === 'map'"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 translate-y-1"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             class="h-[500px] rounded-xl border border-gray-200 bg-white shadow-sm">
                            <div id="map" class="h-full w-full rounded-xl"></div>
                                </div>
                            @endif
                        @endif
        </div>
    </div>
</div>

    <!-- Image Preview Modal -->
    <div x-show="showImagePreview"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center"
         @click.self="showImagePreview = false">
        <div class="relative max-h-[90vh] max-w-4xl rounded-2xl bg-white p-2 shadow-2xl">
            <button @click="showImagePreview = false"
                    class="absolute -right-4 -top-4 rounded-full bg-white p-2 text-gray-400 shadow-lg hover:text-gray-500 focus:outline-none">
                                <span class="sr-only">Close</span>
                                <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
            <img :src="currentImage" class="max-h-[85vh] rounded-xl" alt="Preview">
        </div>
    </div>
</div>
