<div>
    <x-dialog-modal wire:model.live="showModal" maxWidth="4xl">
        <x-slot name="title">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-medium">
                    {{ $item ? $item->title : 'Item Details' }}
                </h3>
                <span class="px-3 py-1 text-sm {{ $item?->status === 'found' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }} rounded-full">
                    {{ ucfirst($item?->status ?? '') }}
                </span>
            </div>
        </x-slot>

        <x-slot name="content">
            @if($item)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Image Gallery -->
                    <div class="space-y-4">
                        @if ($item->images->isNotEmpty())
                            <div class="relative aspect-w-4 aspect-h-3 bg-gray-100 rounded-lg overflow-hidden">
                                <!-- Main Image -->
                                @foreach ($item->images as $index => $image)
                                    <img src="{{ $image->url }}"
                                         alt="{{ $item->title }}"
                                         class="w-full h-full object-contain transition-opacity duration-300"
                                         x-show="$wire.activeImageIndex === {{ $index }}"
                                         wire:key="image-{{ $image->id }}">
                                @endforeach

                                <!-- Navigation Arrows -->
                                @if ($item->images->count() > 1)
                                    <button wire:click="previousImage"
                                            class="absolute left-2 top-1/2 -translate-y-1/2 p-2 rounded-full bg-black/20 hover:bg-black/40 text-white backdrop-blur-sm transition-all duration-200">
                                        <i class="fas fa-chevron-left"></i>
                                    </button>
                                    <button wire:click="nextImage"
                                            class="absolute right-2 top-1/2 -translate-y-1/2 p-2 rounded-full bg-black/20 hover:bg-black/40 text-white backdrop-blur-sm transition-all duration-200">
                                        <i class="fas fa-chevron-right"></i>
                                    </button>

                                    <!-- Image Counter -->
                                    <div class="absolute top-2 left-2 px-3 py-1 rounded-full bg-black/20 backdrop-blur-sm text-white text-sm">
                                        {{ $activeImageIndex + 1 }} / {{ $item->images->count() }}
                                    </div>
                                @endif
                            </div>

                            <!-- Thumbnails -->
                            @if ($item->images->count() > 1)
                                <div class="flex space-x-2 overflow-x-auto pb-2">
                                    @foreach ($item->images as $index => $image)
                                        <button wire:click="setActiveImage({{ $index }})"
                                                class="flex-none w-20 h-20 rounded-lg overflow-hidden transition-all duration-200 {{ $activeImageIndex === $index ? 'ring-2 ring-blue-500 ring-offset-2' : 'opacity-60 hover:opacity-100' }}">
                                            <img src="{{ $image->url }}"
                                                 alt="{{ $item->title }}"
                                                 class="w-full h-full object-cover">
                                        </button>
                                    @endforeach
                                </div>
                            @endif
                        @else
                            <div class="aspect-w-4 aspect-h-3 bg-gray-100 rounded-lg flex items-center justify-center">
                                <div class="text-center text-gray-400">
                                    <i class="fas fa-image text-4xl mb-2"></i>
                                    <p>No images available</p>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Item Details -->
                    <div class="space-y-6">
                        <!-- Description -->
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Description</h4>
                            <p class="mt-1 text-sm text-gray-900">{{ $item->description }}</p>
                        </div>

                        <!-- Category -->
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Category</h4>
                            <p class="mt-1 text-sm text-gray-900">{{ $item->category->name }}</p>
                        </div>

                        <!-- Location -->
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Location</h4>
                            <p class="mt-1 text-sm text-gray-900">
                                {{ $item->location_type === 'specific' ? $item->location_address : $item->area }}
                            </p>
                            @if($item->landmarks)
                                <p class="mt-1 text-sm text-gray-500">Near: {{ $item->landmarks }}</p>
                            @endif
                        </div>

                        <!-- Date Information -->
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">
                                {{ $item->item_type === 'found' ? 'Date Found' : 'Date Lost' }}
                            </h4>
                            <p class="mt-1 text-sm text-gray-900">
                                {{ $item->date_found?->format('F j, Y') ?? $item->date_lost?->format('F j, Y') ?? 'Not specified' }}
                            </p>
                        </div>

                        <!-- Additional Details -->
                        <div class="grid grid-cols-2 gap-4">
                            @if($item->brand)
                                <div>
                                    <h4 class="text-sm font-medium text-gray-500">Brand</h4>
                                    <p class="mt-1 text-sm text-gray-900">{{ $item->brand }}</p>
                                </div>
                            @endif

                            @if($item->color)
                                <div>
                                    <h4 class="text-sm font-medium text-gray-500">Color</h4>
                                    <p class="mt-1 text-sm text-gray-900">{{ $item->color }}</p>
                                </div>
                            @endif

                            @if($item->condition)
                                <div>
                                    <h4 class="text-sm font-medium text-gray-500">Condition</h4>
                                    <p class="mt-1 text-sm text-gray-900">{{ ucfirst(str_replace('_', ' ', $item->condition)) }}</p>
                                </div>
                            @endif
                        </div>

                        <!-- Reporter Information -->
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Reported By</h4>
                            <div class="mt-2 flex items-center">
                                @if(!$item->is_anonymous)
                                    <img src="{{ $item->user->profile_photo_url }}"
                                         alt="{{ $item->user->name }}"
                                         class="h-8 w-8 rounded-full">
                                    <span class="ml-2 text-sm text-gray-900">{{ $item->user->name }}</span>
                                @else
                                    <span class="text-sm text-gray-900">Anonymous</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="flex items-center justify-center h-48">
                    <div class="text-center text-gray-500">
                        <i class="fas fa-circle-notch fa-spin text-2xl mb-2"></i>
                        <p>Loading item details...</p>
                    </div>
                </div>
            @endif
        </x-slot>

        <x-slot name="footer">
            <div class="flex justify-between w-full">
                <div>
                    <x-secondary-button wire:click="closeModal" wire:loading.attr="disabled">
                        Close
                    </x-secondary-button>
                </div>
                <div class="space-x-2">
                    @if($item && $item->status === 'found' && !$item->claimed_by)
                        <x-button wire:click="claimItem({{ $item->id }})" class="bg-green-600 hover:bg-green-700">
                            <i class="fas fa-hand-holding-heart mr-2"></i>
                            Claim Item
                        </x-button>
                    @endif
                    @if($item && $item->status === 'lost')
                        <x-button wire:click="reportMatch({{ $item->id }})" class="bg-blue-600 hover:bg-blue-700">
                            <i class="fas fa-link mr-2"></i>
                            Report Match
                        </x-button>
                    @endif
                </div>
            </div>
        </x-slot>
    </x-dialog-modal>
</div>
