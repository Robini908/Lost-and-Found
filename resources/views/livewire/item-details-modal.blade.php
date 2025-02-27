<div>
    <x-dialog-modal wire:model.live="showModal">
        <x-slot name="title">
            {{ $item ? $item->title : 'Item Details' }}
        </x-slot>

        <x-slot name="content">
            @if($item)
                <!-- Description -->
                <div class="mb-4">
                    <h3 class="text-sm font-medium text-gray-500">Description</h3>
                    <p class="mt-1 text-sm text-gray-900">{{ $item->description }}</p>
                </div>

                <!-- Images -->
                @if ($item->images->isNotEmpty())
                    <div class="mb-4">
                        <h3 class="text-sm font-medium text-gray-500">Images</h3>
                        <div class="mt-2 grid grid-cols-2 gap-2">
                            @foreach ($item->images as $image)
                                <img src="{{ asset('storage/' . $image->image_path) }}"
                                     alt="{{ $item->title }}"
                                     class="h-32 w-full object-cover rounded-lg">
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Location -->
                <div class="mb-4">
                    <h3 class="text-sm font-medium text-gray-500">Location</h3>
                    <p class="mt-1 text-sm text-gray-900">{{ $item->location }}</p>
                </div>

                <!-- Date Information -->
                <div class="mb-4">
                    <h3 class="text-sm font-medium text-gray-500">
                        {{ $item->item_type === 'found' ? 'Date Found' : 'Date Lost' }}
                    </h3>
                    <p class="mt-1 text-sm text-gray-900">
                        @if($item->item_type === 'found')
                            {{ $item->date_found ? $item->date_found->format('F j, Y') : 'Not specified' }}
                        @else
                            {{ $item->date_lost ? $item->date_lost->format('F j, Y') : 'Not specified' }}
                        @endif
                    </p>
                </div>

                <!-- Condition -->
                <div class="mb-4">
                    <h3 class="text-sm font-medium text-gray-500">Condition</h3>
                    <p class="mt-1 text-sm text-gray-900">{{ $item->condition }}</p>
                </div>

                <!-- Reported By -->
                <div class="mb-4">
                    <h3 class="text-sm font-medium text-gray-500">Reported By</h3>
                    <p class="mt-1 text-sm text-gray-900">
                        @if ($item->is_anonymous)
                            Anonymous
                        @else
                            {{ $item->user->name }}
                        @endif
                    </p>
                </div>
            @else
                <div class="text-center py-6">
                    <p class="text-gray-500">Loading item details...</p>
                </div>
            @endif
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="closeModal" wire:loading.attr="disabled">
                Close
            </x-secondary-button>
        </x-slot>
    </x-dialog-modal>
</div>
