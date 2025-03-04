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
                <div class="p-6 space-y-6 bg-white rounded-xl shadow-sm hover:shadow-md transition-all duration-300">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-medium text-gray-900 flex items-center">
                            <i class="fas fa-images text-blue-600 mr-2"></i>
                            Images
                            <span class="ml-2 text-sm text-gray-500">(Upload clear, high-quality photos)</span>
                        </h3>
                        @if(count($existingImages) > 0)
                            <span class="text-sm text-gray-500 bg-gray-100 px-3 py-1 rounded-full">
                                <i class="fas fa-photo-film mr-1.5"></i>
                                {{ count($existingImages) }} photos
                            </span>
                        @endif
                    </div>

                    <!-- Existing Images -->
                    @if(count($existingImages) > 0)
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mt-4">
                            @foreach($existingImages as $image)
                                <div class="group relative aspect-[4/3] bg-gray-100 rounded-lg overflow-hidden shadow-sm hover:shadow-md transition-all duration-300">
                                    <div class="absolute inset-0 flex items-center justify-center">
                                        <img src="{{ asset('storage/' . $image['image_path']) }}"
                                             alt="Item image"
                                             class="w-full h-full object-contain">
                                    </div>
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-all duration-300">
                                        <div class="absolute bottom-0 left-0 right-0 p-3">
                                            <button type="button"
                                                    wire:click="removeExistingImage({{ $image['id'] }})"
                                                    class="w-full bg-red-500 text-white text-sm font-medium px-3 py-1.5 rounded-md hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-all duration-200 flex items-center justify-center">
                                                <i class="fas fa-trash-alt mr-1.5"></i>
                                                Remove
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <!-- New Images Upload -->
                    <div class="mt-6">
                        <div class="flex items-center justify-center w-full">
                            <label for="images" class="relative w-full h-48 flex flex-col items-center justify-center px-4 py-6 bg-gray-50 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer hover:bg-gray-100 transition-all duration-300">
                                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                    <i class="fas fa-cloud-upload-alt text-4xl text-blue-500 mb-3"></i>
                                    <p class="mb-2 text-sm text-gray-700 font-medium">
                                        <span class="text-blue-600">Click to upload</span> or drag and drop
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        PNG, JPG, GIF up to 5MB
                                    </p>
                                </div>
                                <input id="images" wire:model="images" type="file" class="hidden" multiple accept="image/*">
                            </label>
                        </div>
                        @error('images.*')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Upload Tips -->
                    <div class="mt-4 bg-blue-50 rounded-lg p-4">
                        <h4 class="text-sm font-medium text-blue-800 mb-2 flex items-center">
                            <i class="fas fa-lightbulb text-blue-600 mr-2"></i>
                            Tips for better images:
                        </h4>
                        <ul class="text-sm text-blue-700 space-y-1 ml-6 list-disc">
                            <li>Use good lighting to capture clear details</li>
                            <li>Include multiple angles of the item</li>
                            <li>Ensure the item is in focus</li>
                            <li>Add close-up shots of any identifying marks</li>
                        </ul>
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
