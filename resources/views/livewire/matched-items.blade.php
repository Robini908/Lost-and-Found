<div>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 p-6">
        @foreach ($matchedItems as $item)
            <div class="rounded-xl shadow-lg overflow-hidden transition-all duration-500 ease-in-out transform hover:scale-105 hover:shadow-2xl relative"
                style="background: linear-gradient(145deg, #e0f7fa, #b2ebf2);">

                <!-- Header Section for Badges -->
                <div class="p-4 bg-white bg-opacity-90 flex justify-between items-start">
                    <!-- Item Type Badge -->
                    <div class="flex items-center space-x-2">
                        @php
                            $itemTypeColor = [
                                'reported' => 'bg-purple-500 text-white',
                                'searched' => 'bg-yellow-500 text-white',
                                'found' => 'bg-green-500 text-white',
                            ][$item->item_type] ?? 'bg-gray-500 text-white';
                        @endphp
                        <span class="{{ $itemTypeColor }} px-3 py-1 rounded-full text-xs font-semibold shadow-md">
                            {{ ucfirst($item->item_type) }}
                        </span>
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

                    <!-- Matched Item Information -->
                    <div class="mt-4 p-3 bg-gray-50 rounded-lg">
                        <h4 class="text-sm font-semibold text-gray-700 mb-2">Matched Found Item</h4>
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
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
