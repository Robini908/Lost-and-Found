<!-- Step 4: Location Information -->
<div class="space-y-8" x-data="{
    locationType: @entangle('locationType'),
    hoveredType: null
}">
    <div class="text-center mb-8">
        <h2 class="text-2xl font-bold text-gray-900 mb-3">Location Information</h2>
        <p class="mt-2 text-sm text-gray-600 max-w-2xl mx-auto">Help us pinpoint where the item was {{ $reportType === 'found' ? 'found' : 'lost' }}</p>
    </div>

    <!-- Location Type Toggle -->
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 max-w-3xl mx-auto">
        <!-- Specific Location Option -->
        <div class="relative transform transition-all duration-300 hover:scale-105"
             x-on:mouseenter="hoveredType = 'specific'"
             x-on:mouseleave="hoveredType = null">
            <label class="group block h-full cursor-pointer"
                   :class="{
                       'ring-4 ring-blue-100': hoveredType === 'specific' || locationType === 'specific'
                   }">
                <input type="radio"
                    wire:model="locationType"
                    x-on:change="locationType = 'specific'"
                    name="locationType"
                    value="specific"
                    class="sr-only">
                <div class="relative overflow-hidden rounded-xl border-2 transition-all duration-500"
                     :class="{
                         'border-blue-600 shadow-lg': locationType === 'specific',
                         'border-gray-200 hover:border-blue-400': locationType !== 'specific',
                         'border-blue-400 shadow-md': hoveredType === 'specific' && locationType !== 'specific',
                         'bg-gradient-to-br from-blue-50 via-white to-blue-100': locationType === 'specific',
                         'bg-white': locationType !== 'specific'
                     }">
                    <div class="p-6">
                        <div class="flex items-center justify-center w-16 h-16 mx-auto mb-4 rounded-full bg-gradient-to-br from-blue-100 to-blue-50 group-hover:from-blue-200 group-hover:to-blue-100 transition-all duration-300">
                            <svg class="w-8 h-8 transition-colors duration-300"
                                 :class="{
                                     'text-blue-600': locationType === 'specific' || hoveredType === 'specific',
                                     'text-blue-400': locationType !== 'specific' && hoveredType !== 'specific'
                                 }"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </div>
                        <div class="text-center">
                            <h3 class="text-lg font-semibold transition-colors duration-300"
                                :class="{
                                    'text-blue-700': locationType === 'specific' || hoveredType === 'specific',
                                    'text-gray-900': locationType !== 'specific' && hoveredType !== 'specific'
                                }">
                                Specific Location
                            </h3>
                            <p class="mt-2 text-sm text-gray-500">I know the exact location</p>
                        </div>
                    </div>
                </div>
            </label>
        </div>

        <!-- General Area Option -->
        <div class="relative transform transition-all duration-300 hover:scale-105"
             x-on:mouseenter="hoveredType = 'area'"
             x-on:mouseleave="hoveredType = null">
            <label class="group block h-full cursor-pointer"
                   :class="{
                       'ring-4 ring-purple-100': hoveredType === 'area' || locationType === 'area'
                   }">
                <input type="radio"
                    wire:model="locationType"
                    x-on:change="locationType = 'area'"
                    name="locationType"
                    value="area"
                    class="sr-only">
                <div class="relative overflow-hidden rounded-xl border-2 transition-all duration-500"
                     :class="{
                         'border-purple-600 shadow-lg': locationType === 'area',
                         'border-gray-200 hover:border-purple-400': locationType !== 'area',
                         'border-purple-400 shadow-md': hoveredType === 'area' && locationType !== 'area',
                         'bg-gradient-to-br from-purple-50 via-white to-purple-100': locationType === 'area',
                         'bg-white': locationType !== 'area'
                     }">
                    <div class="p-6">
                        <div class="flex items-center justify-center w-16 h-16 mx-auto mb-4 rounded-full bg-gradient-to-br from-purple-100 to-purple-50 group-hover:from-purple-200 group-hover:to-purple-100 transition-all duration-300">
                            <svg class="w-8 h-8 transition-colors duration-300"
                                 :class="{
                                     'text-purple-600': locationType === 'area' || hoveredType === 'area',
                                     'text-purple-400': locationType !== 'area' && hoveredType !== 'area'
                                 }"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                            </svg>
                        </div>
                        <div class="text-center">
                            <h3 class="text-lg font-semibold transition-colors duration-300"
                                :class="{
                                    'text-purple-700': locationType === 'area' || hoveredType === 'area',
                                    'text-gray-900': locationType !== 'area' && hoveredType !== 'area'
                                }">
                                General Area
                            </h3>
                            <p class="mt-2 text-sm text-gray-500">I know the approximate area</p>
                        </div>
                    </div>
                </div>
            </label>
        </div>
    </div>

    <!-- Dynamic Form Fields -->
    <div class="mt-8 max-w-3xl mx-auto">
        <div x-show="locationType === 'specific'"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform translate-y-4"
             x-transition:enter-end="opacity-100 transform translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 transform translate-y-0"
             x-transition:leave-end="opacity-0 transform translate-y-4"
             class="space-y-6">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Specific Location Details</h3>

                <!-- Map Component -->
                <div class="mb-6">
                    <x-map-selector />
                </div>
            </div>
        </div>

        <div x-show="locationType === 'area'"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform translate-y-4"
             x-transition:enter-end="opacity-100 transform translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 transform translate-y-0"
             x-transition:leave-end="opacity-0 transform translate-y-4"
             class="space-y-6">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Area Details</h3>
                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <label for="area" class="block text-sm font-medium text-gray-700">Area Description</label>
                        <div class="mt-1">
                            <input type="text" wire:model="area" id="area"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                placeholder="e.g. Downtown area, Near Central Park">
                        </div>
                        @error('area')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="landmarks" class="block text-sm font-medium text-gray-700">Nearby Landmarks</label>
                        <div class="mt-1">
                            <textarea wire:model="landmarks" id="landmarks" rows="3"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                placeholder="List any notable landmarks or reference points"></textarea>
                        </div>
                        @error('landmarks')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
