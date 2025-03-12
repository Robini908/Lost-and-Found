<div class="relative group">
    @if($item->images->isNotEmpty())
        <div class="relative w-full h-48 bg-gray-100 rounded-t-xl overflow-hidden">
            <img src="{{ Storage::url($item->images->first()->image_path) }}"
                 alt="{{ $item->title }}"
                 class="w-full h-full object-contain hover:cursor-zoom-in"
                 @click="showImage('{{ Storage::url($item->images->first()->image_path) }}', '{{ $item->title }}')">

            <!-- Image Interaction Overlay -->
            <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-10 transition-opacity flex items-center justify-center">
                <div class="opacity-0 group-hover:opacity-100 transform translate-y-1 group-hover:translate-y-0 transition-all flex space-x-2">
                    <button @click.stop="showImage('{{ Storage::url($item->images->first()->image_path) }}', '{{ $item->title }}')"
                            class="p-1.5 bg-white/90 backdrop-blur-sm rounded-full hover:bg-white transition-colors">
                        <svg class="w-4 h-4 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7" />
                        </svg>
                    </button>
                    @if($item->images->count() > 1)
                        <button @click.stop="$dispatch('open-gallery', { images: {{ json_encode($item->images->map->only('image_path')) }} })"
                                class="p-1.5 bg-white/90 backdrop-blur-sm rounded-full hover:bg-white transition-colors">
                            <svg class="w-4 h-4 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span class="absolute -top-1 -right-1 bg-indigo-600 text-white text-xs w-4 h-4 flex items-center justify-center rounded-full text-[10px]">
                                {{ $item->images->count() }}
                            </span>
                        </button>
                    @endif
                </div>
            </div>
        </div>
    @else
        <div class="w-full h-48 bg-gray-100 rounded-t-xl flex items-center justify-center">
            <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
        </div>
    @endif

    <div class="p-3 sm:p-4">
        <!-- Status Badge -->
        @if($showMatched || $item->status)
            <div class="absolute top-2 right-2">
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                    {{ $showMatched ? 'bg-green-100 text-green-800' :
                      ($item->status === 'active' ? 'bg-blue-100 text-blue-800' :
                       ($item->status === 'pending' ? 'bg-yellow-100 text-yellow-800' :
                        'bg-gray-100 text-gray-800')) }}">
                    {{ $showMatched ? 'Matched' : ucfirst($item->status) }}
                </span>
            </div>
        @endif

        <!-- Title and Description -->
        <div class="space-y-1.5">
            <h3 class="text-base font-semibold text-gray-900 group-hover:text-indigo-600 transition-colors line-clamp-2">
                {{ $item->title }}
            </h3>
            <div x-data="{ expanded: false }" class="relative">
                <p class="text-sm text-gray-600" :class="{ 'line-clamp-2': !expanded }">
                    {{ $item->description }}
                </p>
                @if(strlen($item->description) > 120)
                    <button @click="expanded = !expanded"
                            class="text-xs text-indigo-600 hover:text-indigo-800 font-medium mt-0.5 focus:outline-none">
                        <span x-text="expanded ? 'Show less' : 'Read more'"></span>
                    </button>
                @endif
            </div>
        </div>

        <!-- Key Details -->
        <div class="mt-3 grid grid-cols-2 gap-2 text-xs">
            <div class="flex items-center text-gray-600">
                <svg class="w-4 h-4 text-gray-400 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                {{ $item->created_at->format('M d, Y') }}
            </div>
            <div class="flex items-center text-gray-600">
                <svg class="w-4 h-4 text-gray-400 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                </svg>
                {{ $item->category->name }}
            </div>
            @if($item->brand)
                <div class="flex items-center text-gray-600">
                    <svg class="w-4 h-4 text-gray-400 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 4v12l-4-2-4 2V4M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    {{ $item->brand }}
                </div>
            @endif
            @if($item->color)
                <div class="flex items-center text-gray-600">
                    <span class="w-3 h-3 rounded-full mr-1.5" style="background-color: {{ $item->color }}"></span>
                    {{ $item->color }}
                </div>
            @endif
        </div>

        @if($item->location_address)
            <div class="mt-2 flex items-center text-xs text-gray-600">
                <svg class="w-4 h-4 text-gray-400 mr-1.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <span class="truncate">{{ $item->location_address }}</span>
            </div>
        @endif

        <!-- Actions -->
        @if(!$showMatched)
            <div class="mt-4 flex flex-wrap items-center gap-2">
                <button wire:click="findMatches({{ $item->id }})"
                        class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                    <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    Find Matches
                </button>

                <button wire:click="$emit('openShareModal', {{ $item->id }})"
                        class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors">
                    <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
                    </svg>
                    Share
                </button>
            </div>
        @else
            <div class="mt-4 flex flex-wrap items-center gap-2">
                <button wire:click="viewMatchDetails({{ $item->id }})"
                        class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                    <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    View Match Details
                </button>

                <button wire:click="unmatchItem({{ $item->id }})"
                        class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors">
                    <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    Unmatch
                </button>
            </div>
        @endif
    </div>
</div>

<!-- Image Modal -->
<div x-show="showImageModal"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 transform scale-90"
     x-transition:enter-end="opacity-100 transform scale-100"
     x-transition:leave="transition ease-in duration-300"
     x-transition:leave-start="opacity-100 transform scale-100"
     x-transition:leave-end="opacity-0 transform scale-90"
     class="fixed inset-0 z-50 overflow-y-auto"
     @click.self="showImageModal = false">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 bg-black opacity-75" @click="showImageModal = false"></div>
        <div class="relative bg-white rounded-lg max-w-4xl w-full">
            <div class="absolute top-0 right-0 pt-4 pr-4">
                <button @click="showImageModal = false" class="text-gray-400 hover:text-gray-500">
                    <span class="sr-only">Close</span>
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="p-4">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium" x-text="selectedImageTitle"></h3>
                    <div class="flex items-center space-x-4">
                        <button @click="toggleZoom('out')" class="p-2 hover:bg-gray-100 rounded-full">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                            </svg>
                        </button>
                        <span x-text="`${imageZoom}%`" class="text-sm text-gray-600"></span>
                        <button @click="toggleZoom('in')" class="p-2 hover:bg-gray-100 rounded-full">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="relative overflow-hidden rounded-lg" style="height: 70vh;">
                    <img :src="selectedImage"
                         :style="'transform: scale(' + (imageZoom/100) + ')'"
                         class="transition-transform duration-300 absolute inset-0 w-full h-full object-contain">
                </div>
            </div>
        </div>
    </div>
</div>
