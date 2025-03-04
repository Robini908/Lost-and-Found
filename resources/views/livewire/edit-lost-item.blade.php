<div class="min-h-screen bg-gradient-to-b from-blue-50 to-white py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header Section -->
        <div class="mb-8">
            <h2 class="text-3xl font-bold text-gray-900">
                Edit Item Details
            </h2>
            <p class="mt-1 text-sm text-gray-600">
                Update the information for your {{ $status }} item
            </p>
        </div>

        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
            <form wire:submit="submit" class="divide-y divide-gray-200">
                <!-- Basic Information Section -->
                <div class="p-6 space-y-6">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-medium text-gray-900">Basic Information</h3>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $status === 'lost' ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                            {{ ucfirst($status) }} Item
                        </span>
                    </div>

                    <div class="space-y-6">
                        <!-- Title -->
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
                            <div class="mt-1">
                                <input type="text" wire:model="title" id="title"
                                    class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                    placeholder="Brief title of the item">
                            </div>
                            @error('title')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                            <div class="mt-1">
                                <textarea wire:model="description" id="description" rows="4"
                                    class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                    placeholder="Detailed description of the item"></textarea>
                            </div>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Category -->
                        <div>
                            <label for="category_id" class="block text-sm font-medium text-gray-700">Category</label>
                            <div class="mt-1">
                                <select wire:model="category_id" id="category_id"
                                    class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                    <option value="">Select a category</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @error('category_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Brand -->
                        <div>
                            <label for="brand" class="block text-sm font-medium text-gray-700">Brand</label>
                            <div class="mt-1">
                                <input type="text" wire:model="brand" id="brand"
                                    class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                    placeholder="Brand name (if applicable)">
                            </div>
                            @error('brand')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Color -->
                        <div>
                            <label for="color" class="block text-sm font-medium text-gray-700">Color</label>
                            <div class="mt-1">
                                <input type="text" wire:model="color" id="color"
                                    class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                    placeholder="Primary color of the item">
                            </div>
                            @error('color')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Condition -->
                        <div>
                            <label for="condition" class="block text-sm font-medium text-gray-700">Condition</label>
                            <div class="mt-1">
                                <select wire:model="condition" id="condition"
                                    class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                    <option value="">Select condition</option>
                                    <option value="new">New</option>
                                    <option value="like_new">Like New</option>
                                    <option value="excellent">Excellent</option>
                                    <option value="good">Good</option>
                                    <option value="fair">Fair</option>
                                    <option value="poor">Poor</option>
                                    <option value="damaged">Damaged</option>
                                </select>
                            </div>
                            @error('condition')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Date -->
                        <div>
                            <label for="date" class="block text-sm font-medium text-gray-700">
                                {{ $status === 'lost' ? 'Date Lost' : 'Date Found' }}
                            </label>
                            <div class="mt-1">
                                <input type="date" wire:model="date" id="date"
                                    class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            </div>
                            @error('date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Additional Notes -->
                        <div>
                            <label for="notes" class="block text-sm font-medium text-gray-700">Additional Notes</label>
                            <div class="mt-1">
                                <textarea wire:model="notes" id="notes" rows="3"
                                    class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                    placeholder="Any additional details that might help identify the item"></textarea>
                            </div>
                            @error('notes')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Anonymous Reporting -->
                        <div class="relative flex items-start">
                            <div class="flex items-center h-5">
                                <input wire:model="is_anonymous" id="is_anonymous" type="checkbox"
                                    class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="is_anonymous" class="font-medium text-gray-700">Report Anonymously</label>
                                <p class="text-gray-500">Your identity will not be shown publicly</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Location Section -->
                <div class="p-6 space-y-6">
                    <h3 class="text-lg font-medium text-gray-900">Location Information</h3>

                    <div class="space-y-6">
                        <!-- Location Type -->
                        <div class="flex items-center space-x-4">
                            <label class="flex items-center">
                                <input type="radio" wire:model="locationType" value="specific" class="form-radio h-4 w-4 text-blue-600">
                                <span class="ml-2">Specific Location</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" wire:model="locationType" value="area" class="form-radio h-4 w-4 text-blue-600">
                                <span class="ml-2">General Area</span>
                            </label>
                        </div>

                        @if($locationType === 'specific')
                            <div class="space-y-4">
                                <x-map-selector
                                    wire:model.live="location_address"
                                    :lat="$location_lat"
                                    :lng="$location_lng"
                                    class="w-full h-64 rounded-lg shadow-sm"
                                />
                                <x-input-error for="location_address" class="mt-2" />
                                <x-input-error for="location_lat" class="mt-2" />
                                <x-input-error for="location_lng" class="mt-2" />
                            </div>
                        @else
                            <div>
                                <x-label for="area" value="Area Description" />
                                <x-textarea wire:model="area" class="mt-1 block w-full" rows="2" placeholder="Describe the general area..." />
                                <x-input-error for="area" class="mt-2" />
                            </div>
                        @endif

                        <!-- Landmarks -->
                        <div>
                            <x-label for="landmarks" value="Nearby Landmarks" />
                            <x-textarea wire:model="landmarks" class="mt-1 block w-full" rows="2" placeholder="Describe any nearby landmarks..." />
                            <x-input-error for="landmarks" class="mt-2" />
                        </div>
                    </div>
                </div>

                <!-- Images Section -->
                <div class="p-6 space-y-6">
                    <h3 class="text-lg font-medium text-gray-900">Images</h3>

                    <!-- Existing Images -->
                    @if(count($existingImages) > 0)
                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                            @foreach($existingImages as $image)
                                <div class="relative group">
                                    <img src="{{ asset('storage/' . $image['image_path']) }}"
                                         alt="Item image"
                                         class="w-full h-32 object-cover rounded-lg shadow-sm">
                                    <button type="button"
                                            wire:click="removeExistingImage({{ $image['id'] }})"
                                            class="absolute top-2 right-2 p-1 bg-red-500 text-white rounded-full opacity-0 group-hover:opacity-100 transition-opacity">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <!-- New Images Upload -->
                    <div class="mt-4">
                        <x-label for="images" value="Add New Images" />
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg">
                            <div class="space-y-1 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <div class="flex text-sm text-gray-600">
                                    <label for="images" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                        <span>Upload new images</span>
                                        <input id="images" wire:model="images" type="file" class="sr-only" multiple accept="image/*">
                                    </label>
                                    <p class="pl-1">or drag and drop</p>
                                </div>
                                <p class="text-xs text-gray-500">PNG, JPG, GIF up to 5MB</p>
                            </div>
                        </div>
                        <x-input-error for="images.*" class="mt-2" />
                    </div>
                </div>

                <!-- Additional Information -->
                <div class="p-6 space-y-6">
                    <h3 class="text-lg font-medium text-gray-900">Additional Information</h3>

                    <div class="space-y-6">
                        <!-- Notes -->
                        <div>
                            <x-label for="notes" value="Additional Notes" />
                            <x-textarea wire:model="notes" class="mt-1 block w-full" rows="3" placeholder="Any additional details that might help identify the item..." />
                            <x-input-error for="notes" class="mt-2" />
                        </div>

                        <!-- Anonymous Reporting -->
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input wire:model="is_anonymous" type="checkbox" class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="is_anonymous" class="font-medium text-gray-700">Report Anonymously</label>
                                <p class="text-gray-500">Your contact information will not be shared with others</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="px-6 py-4 bg-gray-50 flex justify-between items-center">
                    <div class="flex space-x-3">
                        <button type="button"
                                wire:click="cancel"
                                class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <i class="fas fa-arrow-left mr-1.5"></i>
                            Back
                        </button>
                        <button type="button"
                                wire:click="deleteItem"
                                wire:confirm="Are you sure you want to delete this item? This action cannot be undone."
                                class="inline-flex items-center px-4 py-2 border border-red-300 shadow-sm text-sm font-medium rounded-md text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                            <i class="fas fa-trash mr-1.5"></i>
                            Delete
                        </button>
                    </div>
                    <button type="submit"
                            class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fas fa-save mr-1.5"></i>
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
