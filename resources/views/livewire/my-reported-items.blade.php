<div class="bg-gradient-to-b from-blue-50 to-white">
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="md:flex md:items-center md:justify-between mb-6">
            <div class="flex-1 min-w-0">
                <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                    {{ $isEditing ? 'Edit Item' : 'My Reported Items' }}
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    {{ $isEditing ? 'Update your item details' : 'Manage your lost and found items' }}
                </p>
            </div>
        </div>

        @if(!$isEditing)
            <!-- Filters -->
            <div class="mb-6 bg-white rounded-lg shadow p-4 sm:p-6">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    <!-- Search -->
                    <div>
                        <label for="search" class="sr-only">Search</label>
                        <div class="relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-search text-gray-400"></i>
                            </div>
                            <input type="text" wire:model.live.debounce.300ms="search"
                                   class="focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 sm:text-sm border-gray-300 rounded-md"
                                   placeholder="Search items...">
                        </div>
                    </div>

                    <!-- Status Filter -->
                    <div>
                        <select wire:model.live="status" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                            <option value="">All Status</option>
                            <option value="lost">Lost Items</option>
                            <option value="found">Found Items</option>
                            <option value="claimed">Claimed Items</option>
                            <option value="returned">Returned Items</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Items Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($items as $item)
                    <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition-all duration-300 overflow-hidden group">
                        <!-- Image Container -->
                        <div class="relative aspect-[4/3] bg-gray-100 overflow-hidden">
                            <div class="absolute inset-0 flex items-center justify-center">
                                @if($item->images->count() > 0)
                                    <img src="{{ asset('storage/' . $item->images->first()->image_path) }}"
                                         alt="{{ $item->title }}"
                                         class="w-full h-full object-contain">
                                    @if($item->images->count() > 1)
                                        <div class="absolute top-2 right-2">
                                            <span class="px-2 py-1 text-xs font-medium text-white bg-black/50 rounded-full backdrop-blur-sm">
                                                <i class="fas fa-images mr-1"></i>
                                                {{ $item->images->count() }}
                                            </span>
                                        </div>
                                    @endif
                                @else
                                    <div class="text-center">
                                        <i class="fas fa-image text-gray-400 text-4xl mb-2"></i>
                                        <p class="text-sm text-gray-400">No image available</p>
                                    </div>
                                @endif
                            </div>
                            <!-- Hover Overlay -->
                            <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/0 opacity-0 group-hover:opacity-100 transition-all duration-300">
                                <div class="absolute bottom-0 left-0 right-0 p-4">
                                    <div class="flex items-center space-x-2">
                                        <button type="button"
                                            wire:click="editItem({{ $item->id }})"
                                            class="flex-1 inline-flex items-center justify-center px-4 py-2 bg-white/90 backdrop-blur-sm text-sm font-medium rounded-lg text-gray-700 hover:bg-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200">
                                            <i class="fas fa-edit mr-2"></i>
                                            Edit
                                        </button>
                                        @if($item->status === 'lost')
                                            <button type="button"
                                                    wire:click="$dispatch('openModal', { component: 'mark-as-found', arguments: { itemId: {{ $item->id }} }})"
                                                    class="flex-1 inline-flex items-center justify-center px-4 py-2 bg-green-500/90 backdrop-blur-sm text-sm font-medium rounded-lg text-white hover:bg-green-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-200">
                                                <i class="fas fa-check-circle mr-2"></i>
                                                Found
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="p-5">
                            <div class="flex items-start justify-between mb-3">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900 mb-1 line-clamp-1">
                                        {{ $item->title }}
                                    </h3>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        {{ $item->status === 'lost' ? 'bg-red-100 text-red-800' : '' }}
                                        {{ $item->status === 'found' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $item->status === 'claimed' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                        {{ $item->status === 'returned' ? 'bg-blue-100 text-blue-800' : '' }}">
                                        <i class="fas fa-circle text-[8px] mr-1.5"></i>
                                        {{ ucfirst($item->status) }}
                                    </span>
                                </div>
                            </div>

                            <p class="text-sm text-gray-600 line-clamp-2 mb-4">
                                {{ $item->description }}
                            </p>

                            <div class="flex items-center justify-between text-sm">
                                <div class="flex items-center text-gray-500">
                                    <i class="fas fa-calendar-alt mr-1.5 text-gray-400"></i>
                                    {{ $item->created_at->format('M d, Y') }}
                                </div>
                                <div class="flex items-center text-gray-500">
                                    <i class="fas fa-map-marker-alt mr-1.5 text-gray-400"></i>
                                    {{ Str::limit($item->location_type === 'map' ? $item->location_address : $item->area, 20) }}
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full">
                        <div class="flex flex-col items-center justify-center py-12 bg-white rounded-xl shadow-sm text-center">
                            <div class="w-20 h-20 rounded-full bg-blue-50 flex items-center justify-center mb-4">
                                <i class="fas fa-box-open text-blue-500 text-3xl"></i>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">No items found</h3>
                            <p class="text-sm text-gray-500 mb-6">
                                You haven't reported any items yet.
                            </p>
                            <a href="{{ route('products.report-item') }}"
                               class="inline-flex items-center px-6 py-2.5 border border-transparent text-sm font-medium rounded-full text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200">
                                <i class="fas fa-plus mr-2"></i>
                                Report an Item
                            </a>
                        </div>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $items->links() }}
            </div>
        @else
            <!-- Edit Form -->
            <div class="bg-white rounded-lg shadow-sm" x-data="{ activeTab: 'basic' }">
                <!-- Material Design Header -->
                <div class="bg-white px-6 py-4 border-b">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <button type="button" wire:click="closeEdit"
                                class="p-2 rounded-full hover:bg-gray-100 transition-colors duration-200">
                                <i class="fas fa-arrow-left text-gray-600"></i>
                            </button>
                            <div>
                                <h3 class="text-xl text-gray-900">Edit Item</h3>
                                <p class="text-sm text-gray-600">ID: {{ $editItemId }}</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <button type="button" wire:click="$dispatch('save')"
                                class="inline-flex items-center px-6 py-2 bg-blue-600 text-white text-sm font-medium rounded-full hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                                Save
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Main Content Area -->
                <div class="flex min-h-[calc(100vh-12rem)]">
                    <!-- Left Navigation -->
                    <div class="w-64 border-r bg-white">
                        <nav class="p-4 space-y-1">
                            <button @click="activeTab = 'basic'"
                                class="w-full flex items-center px-4 py-3 rounded-lg group"
                                :class="activeTab === 'basic' ? 'text-blue-600 bg-blue-50' : 'text-gray-600 hover:bg-gray-50'">
                                <i class="fas fa-info-circle w-6"></i>
                                <span class="text-sm font-medium">Basic Information</span>
                            </button>
                            <button @click="activeTab = 'images'"
                                class="w-full flex items-center px-4 py-3 rounded-lg group"
                                :class="activeTab === 'images' ? 'text-blue-600 bg-blue-50' : 'text-gray-600 hover:bg-gray-50'">
                                <i class="fas fa-images w-6"></i>
                                <span class="text-sm font-medium">Images</span>
                            </button>
                            <button @click="activeTab = 'location'"
                                class="w-full flex items-center px-4 py-3 rounded-lg group"
                                :class="activeTab === 'location' ? 'text-blue-600 bg-blue-50' : 'text-gray-600 hover:bg-gray-50'">
                                <i class="fas fa-map-marker-alt w-6"></i>
                                <span class="text-sm font-medium">Location</span>
                            </button>
                            <button @click="activeTab = 'history'"
                                class="w-full flex items-center px-4 py-3 rounded-lg group"
                                :class="activeTab === 'history' ? 'text-blue-600 bg-blue-50' : 'text-gray-600 hover:bg-gray-50'">
                                <i class="fas fa-history w-6"></i>
                                <span class="text-sm font-medium">History</span>
                            </button>
                        </nav>
                    </div>

                    <!-- Main Form Area -->
                    <div class="flex-1 min-w-0">
                        <div class="h-full">
                            <div class="px-8 py-6">
                                <!-- Basic Information Tab -->
                                <div x-show="activeTab === 'basic'">
                                    <livewire:edit-lost-item :item-id="$editItemId" wire:key="edit-item-{{ $editItemId }}" />
                                </div>

                                <!-- Images Tab -->
                                <div x-show="activeTab === 'images'" x-cloak>
                                    <div class="space-y-6">
                                        <div class="bg-white rounded-lg">
                                            <div class="p-4">
                                                <h3 class="text-lg font-medium text-gray-900 mb-4">Item Images</h3>
                                                <div class="filepond-wrapper">
                                                    <x-filepond::upload
                                                        wire:model="images"
                                                        multiple
                                                        allowMultiple
                                                        allowFileTypeValidation
                                                        allowFileSizeValidation
                                                        maxFileSize="5mb"
                                                        maxFiles="5"
                                                    />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Location Tab -->
                                <div x-show="activeTab === 'location'" x-cloak>
                                    <div class="space-y-6" x-data="{ selectedType: @entangle('locationType').live }">
                                        <!-- Location Type Selection -->
                                        <div class="bg-white rounded-lg p-6 border border-gray-200">
                                            <h3 class="text-lg font-medium text-gray-900 mb-4">Location Type</h3>
                                            <div class="grid grid-cols-2 gap-6">
                                                <!-- Specific Location Option -->
                                                <div class="relative">
                                                    <input type="radio"
                                                           id="specific-location"
                                                           wire:model.live="locationType"
                                                           name="locationType"
                                                           value="specific"
                                                           class="peer sr-only"
                                                           x-model="selectedType">
                                                    <label for="specific-location"
                                                           class="block p-4 rounded-lg border-2 cursor-pointer transition-all duration-200"
                                                           :class="{
                                                               'border-blue-500 bg-blue-50': selectedType === 'specific',
                                                               'border-gray-200 hover:bg-gray-50': selectedType !== 'specific'
                                                           }">
                                                        <div class="flex items-center justify-between mb-2">
                                                            <div class="flex items-center">
                                                                <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                                                                    <i class="fas fa-map-marker-alt text-blue-600"></i>
                                                                </div>
                                                                <div>
                                                                    <h4 class="font-medium text-gray-900">Specific Location</h4>
                                                                    <p class="text-sm text-gray-500">Exact coordinates on map</p>
                                                                </div>
                                                            </div>
                                                            <div class="w-5 h-5 border-2 rounded-full flex items-center justify-center"
                                                                 :class="{
                                                                     'border-blue-500 bg-blue-500': selectedType === 'specific',
                                                                     'border-gray-300': selectedType !== 'specific'
                                                                 }">
                                                                <i class="fas fa-check text-white text-xs" x-show="selectedType === 'specific'"></i>
                                                            </div>
                                                        </div>
                                                    </label>
                                                </div>

                                                <!-- General Area Option -->
                                                <div class="relative">
                                                    <input type="radio"
                                                           id="area-location"
                                                           wire:model.live="locationType"
                                                           name="locationType"
                                                           value="area"
                                                           class="peer sr-only"
                                                           x-model="selectedType">
                                                    <label for="area-location"
                                                           class="block p-4 rounded-lg border-2 cursor-pointer transition-all duration-200"
                                                           :class="{
                                                               'border-blue-500 bg-blue-50': selectedType === 'area',
                                                               'border-gray-200 hover:bg-gray-50': selectedType !== 'area'
                                                           }">
                                                        <div class="flex items-center justify-between mb-2">
                                                            <div class="flex items-center">
                                                                <div class="w-10 h-10 rounded-full bg-purple-100 flex items-center justify-center mr-3">
                                                                    <i class="fas fa-map text-purple-600"></i>
                                                                </div>
                                                                <div>
                                                                    <h4 class="font-medium text-gray-900">General Area</h4>
                                                                    <p class="text-sm text-gray-500">Approximate location description</p>
                                                                </div>
                                                            </div>
                                                            <div class="w-5 h-5 border-2 rounded-full flex items-center justify-center"
                                                                 :class="{
                                                                     'border-blue-500 bg-blue-500': selectedType === 'area',
                                                                     'border-gray-300': selectedType !== 'area'
                                                                 }">
                                                                <i class="fas fa-check text-white text-xs" x-show="selectedType === 'area'"></i>
                                                            </div>
                                                        </div>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Location Details Grid -->
                                        <div class="grid grid-cols-12 gap-6">
                                            <!-- Map Section -->
                                            <div class="col-span-12 lg:col-span-8">
                                                <div class="bg-white rounded-lg border border-gray-200 overflow-hidden"
                                                     :class="{ 'opacity-50 pointer-events-none': selectedType !== 'specific' }">
                                                    <div class="p-6">
                                                        <h3 class="text-lg font-medium text-gray-900 mb-4">Map Location</h3>
                                                        <div class="aspect-w-16 aspect-h-9 rounded-lg overflow-hidden">
                                                            <x-map-selector />
                                                        </div>
                                                        <div class="mt-4">
                                                            <p class="text-sm text-gray-500">
                                                                <i class="fas fa-info-circle mr-1"></i>
                                                                Drag the marker or click on the map to set the exact location
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Area Details Section -->
                                            <div class="col-span-12 lg:col-span-4">
                                                <div class="bg-white rounded-lg border border-gray-200"
                                                     :class="{ 'opacity-50 pointer-events-none': selectedType !== 'area' }">
                                                    <div class="p-6">
                                                        <h3 class="text-lg font-medium text-gray-900 mb-4">Area Details</h3>
                                                        <div class="space-y-4">
                                                            <div>
                                                                <label class="block text-sm font-medium text-gray-700 mb-1">Area Name/Description</label>
                                                                <input type="text"
                                                                       wire:model="area"
                                                                       class="w-full rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                                                       placeholder="e.g., Downtown Business District"
                                                                       :disabled="selectedType !== 'area'">
                                                            </div>
                                                            <div>
                                                                <label class="block text-sm font-medium text-gray-700 mb-1">Nearby Landmarks</label>
                                                                <textarea wire:model="landmarks"
                                                                          rows="4"
                                                                          class="w-full rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                                                          placeholder="List notable nearby places or landmarks..."
                                                                          :disabled="selectedType !== 'area'"></textarea>
                                                            </div>
                                                            <div class="bg-yellow-50 rounded-lg p-4">
                                                                <div class="flex">
                                                                    <div class="flex-shrink-0">
                                                                        <i class="fas fa-lightbulb text-yellow-400"></i>
                                                                    </div>
                                                                    <div class="ml-3">
                                                                        <p class="text-sm text-yellow-700">
                                                                            Include easily recognizable landmarks and areas to help others locate the item.
                                                                        </p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- History Tab -->
                                <div x-show="activeTab === 'history'" x-cloak>
                                    <div class="space-y-6">
                                        <div class="bg-white rounded-lg">
                                            <div class="p-4">
                                                <h3 class="text-lg font-medium text-gray-900 mb-4">Item History</h3>
                                                <div class="space-y-4">
                                                    <!-- Timeline entries would go here -->
                                                    <div class="flex items-center space-x-4">
                                                        <div class="flex-shrink-0">
                                                            <div class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center">
                                                                <i class="fas fa-plus text-blue-600"></i>
                                                            </div>
                                                        </div>
                                                        <div>
                                                            <p class="text-sm font-medium text-gray-900">Item Created</p>
                                                            <p class="text-sm text-gray-500">{{ now()->format('M d, Y h:i A') }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Info Panel -->
                    <div class="w-80 border-l bg-white">
                        <div class="p-6 space-y-6">
                            <!-- Status Card -->
                            <div class="bg-white rounded-lg">
                                <h4 class="text-sm font-medium text-gray-900 mb-3">Status</h4>
                                <div class="p-4 bg-blue-50 rounded-lg">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0">
                                            <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">
                                                <i class="fas fa-info text-blue-600"></i>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <h4 class="text-sm font-medium text-gray-900">Under Review</h4>
                                            <p class="text-sm text-gray-600">Last updated {{ now()->diffForHumans() }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Quick Actions -->
                            <div>
                                <h4 class="text-sm font-medium text-gray-900 mb-3">Quick Actions</h4>
                                <div class="space-y-3">
                                    <button type="button" wire:click="$dispatch('markAsFound')"
                                        class="w-full flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm text-gray-700 hover:bg-gray-50 transition-colors duration-200">
                                        <i class="fas fa-check-circle w-5 text-green-600"></i>
                                        <span class="ml-3">Mark as Found</span>
                                    </button>
                                    <button type="button" wire:click="$dispatch('archive')"
                                        class="w-full flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm text-gray-700 hover:bg-gray-50 transition-colors duration-200">
                                        <i class="fas fa-archive w-5 text-gray-600"></i>
                                        <span class="ml-3">Archive Item</span>
                                    </button>
                                    <button type="button" wire:click="$dispatch('delete')"
                                        class="w-full flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm text-red-600 hover:bg-red-50 hover:border-red-300 transition-colors duration-200">
                                        <i class="fas fa-trash-alt w-5"></i>
                                        <span class="ml-3">Delete Item</span>
                                    </button>
                                </div>
                            </div>

                            <!-- Item Details -->
                            <div>
                                <h4 class="text-sm font-medium text-gray-900 mb-3">Details</h4>
                                <dl class="space-y-3">
                                    <div class="flex justify-between">
                                        <dt class="text-sm text-gray-600">Created</dt>
                                        <dd class="text-sm text-gray-900">{{ now()->format('M d, Y') }}</dd>
                                    </div>
                                    <div class="flex justify-between">
                                        <dt class="text-sm text-gray-600">Category</dt>
                                        <dd class="text-sm text-gray-900">Electronics</dd>
                                    </div>
                                    <div class="flex justify-between">
                                        <dt class="text-sm text-gray-600">Report Type</dt>
                                        <dd class="text-sm text-gray-900">Lost Item</dd>
                                    </div>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
