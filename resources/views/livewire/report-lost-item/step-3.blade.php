<!-- Step 3: Item Details -->
<div class="space-y-8">
    <div class="text-center mb-8">
        <h2 class="text-2xl font-bold text-gray-900 mb-3">Item Details</h2>
        <p class="mt-2 text-sm text-gray-600 max-w-2xl mx-auto">Please provide as much detail as possible about the item to help with identification.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Brand Field -->
        <div class="relative">
            <label for="brand" class="block text-sm font-medium text-gray-700 mb-2">Brand</label>
            <div class="mt-1 relative rounded-md shadow-sm">
                <input type="text" wire:model="brand" id="brand"
                    class="block w-full rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                    placeholder="e.g., Apple, Samsung, Nike">
            </div>
            @error('brand')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Color Field -->
        <div class="relative">
            <label for="color" class="block text-sm font-medium text-gray-700 mb-2">Color</label>
            <div class="mt-1 relative rounded-md shadow-sm">
                <input type="text" wire:model="color" id="color"
                    class="block w-full rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                    placeholder="e.g., Black, Silver, Blue">
            </div>
            @error('color')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Condition Field -->
        <div class="relative">
            <label for="condition" class="block text-sm font-medium text-gray-700 mb-2">
                Item Condition <span class="text-red-500">*</span>
            </label>
            <div class="mt-1 relative rounded-md shadow-sm">
                <select wire:model="condition" id="condition"
                    class="block w-full rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
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

        <!-- Date Field -->
        <div class="relative">
            <label for="date" class="block text-sm font-medium text-gray-700 mb-2">
                {{ $reportType === 'found' ? 'Date Found' : 'Date Lost' }}
            </label>
            <div class="mt-1 relative rounded-md shadow-sm">
                <input type="date" wire:model="date" id="date"
                    class="block w-full rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
            </div>
            @error('date')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Notes Field -->
        <div class="relative col-span-2">
            <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Additional Notes</label>
            <div class="mt-1 relative rounded-md shadow-sm">
                <textarea wire:model="notes" id="notes" rows="3"
                    class="block w-full rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                    placeholder="Any additional details that might help identify the item..."></textarea>
            </div>
            @error('notes')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <!-- Image Upload Section -->
    <div class="mt-8">
        <div class="bg-white rounded-lg border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-lg font-medium text-gray-900">Upload Images</h3>
                    <p class="mt-1 text-sm text-gray-500">Add up to 5 images of the item</p>
                </div>
                <span class="inline-flex items-center px-3 py-0.5 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                    Max 5MB per image
                </span>
            </div>

            <!-- FilePond Upload Area -->
            <div class="mt-4">
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
                @error('images.*')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Image Guidelines -->
            <div class="mt-4 bg-blue-50 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h4 class="text-sm font-medium text-blue-800">Image Guidelines</h4>
                        <ul class="mt-2 text-sm text-blue-700 space-y-1">
                            <li>• Clear, well-lit photos from multiple angles</li>
                            <li>• Include any distinguishing marks or features</li>
                            <li>• Supported formats: JPEG, PNG, GIF, WebP, BMP, TIFF, SVG</li>
                            <li>• Maximum 5 images, 5MB each</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.filepond-wrapper {
    @apply bg-gray-50 rounded-lg border-2 border-dashed border-gray-300;
}
</style>
