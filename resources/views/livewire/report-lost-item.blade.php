<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
    <div class="flex space-x-6">
        <!-- Reporting Lost Icon -->
        <div class="p-4 rounded-lg cursor-pointer transition-all duration-300 flex items-center justify-center {{ $mode === 'reporting-lost' ? 'bg-blue-100 text-blue-600 border border-blue-200' : 'bg-gray-100 text-gray-500 hover:bg-gray-200 border border-gray-200' }}"
            wire:click="$set('mode', 'reporting-lost')" data-tippy-content="Reporting a Lost Item">
            <i class="fas fa-exclamation-circle text-2xl"></i>
        </div>

        <!-- Searching Icon -->
        <div class="p-4 rounded-lg cursor-pointer transition-all duration-300 flex items-center justify-center {{ $mode === 'searching' ? 'bg-green-100 text-green-600 border border-green-200' : 'bg-gray-100 text-gray-500 hover:bg-gray-200 border border-gray-200' }}"
            wire:click="$set('mode', 'searching')" data-tippy-content="Searching for a Lost Item">
            <i class="fas fa-search text-2xl"></i>
        </div>

        <!-- Reporting Found Icon -->
        <div class="p-4 rounded-lg cursor-pointer transition-all duration-300 flex items-center justify-center {{ $mode === 'reporting-found' ? 'bg-yellow-100 text-yellow-600 border border-yellow-200' : 'bg-gray-100 text-gray-500 hover:bg-gray-200 border border-gray-200' }}"
            wire:click="$set('mode', 'reporting-found')" data-tippy-content="Reporting a Found Item">
            <i class="fas fa-check-circle text-2xl"></i>
        </div>
    </div>

    <div class="flex items-center justify-center mb-4">
        @foreach ([1, 2, 3, 4] as $step)
            <div class="flex items-center space-x-4">
                <div
                    class="flex items-center justify-center w-12 h-12 rounded-full {{ $currentStep >= $step ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-500' }}">
                    <span>{{ $step }}</span>
                </div>
                @if ($step != 4)
                    <div class="w-10 h-1 {{ $currentStep > $step ? 'bg-blue-600' : 'bg-gray-100' }}"></div>
                @endif
            </div>
        @endforeach
    </div>

    <!-- Step 1: Basic Information -->
    @if ($currentStep == 1)
        <div>
            <x-form-section submit="step1" class="space-y-6 p-6 rounded-lg">
                <x-slot name="title">
                    {{ $mode === 'reporting-lost' ? 'Reporting a Lost Item (that is not yours)' : ($mode === 'searching' ? 'Searching for your Lost Item' : 'Reporting a Found Item') }}
                </x-slot>

                <x-slot name="description">
                    {{ $mode === 'reporting-lost' ? 'Please provide the basic information about the lost item.' : ($mode === 'searching' ? 'Please provide the basic information about the item you are searching for.' : 'Please provide the basic information about the found item.') }}
                </x-slot>

                <x-slot name="form">
                    <div class="col-span-6 sm:col-span-3">
                        <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
                        <x-input type="text" wire:model="title" id="title" class="mt-1 block w-full" required
                            autofocus autocomplete="title" />
                        <x-input-error for="title" class="mt-2" />
                    </div>

                    <div class="col-span-6 sm:col-span-3">
                        <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                        <x-textarea wire:model="description" id="description" rows="4"
                            class="mt-1 block w-full"></x-textarea>
                        <x-input-error for="description" class="mt-2" />
                    </div>

                    <div class="col-span-6 sm:col-span-3">
                        <label for="category" class="block text-sm font-medium text-gray-700">Category</label>
                        <div class="flex items-center gap-2">
                            <x-select wire:model.live="category_id" id="category"
                                class="mt-1 block w-full form-select">
                                <option value="">Select a category</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </x-select>
                            <!-- Icon with Tooltip -->
                            <p class="text-sm text-gray-500 mt-2">Category not found, you can add it by clicking <button
                                    type="button" class="text-blue-500 underline"
                                    wire:click="openCategoryModal">Add</button></p>
                        </div>
                        <x-input-error for="category_id" class="mt-2" />
                    </div>

                    <div class="col-span-6 sm:col-span-3">
                        <label for="location" class="block text-sm font-medium text-gray-700">Location</label>
                        <x-input type="text" wire:model="location" id="location" class="mt-1 block w-full" />
                        <x-input-error for="location" class="mt-2" />
                    </div>

                    <div class="col-span-6 sm:col-span-3">
                        <label for="map" class="block text-sm font-medium text-gray-700">Select Location on
                            Map</label>
                        <div id="map" style="height: 400px;"></div>
                        <input type="hidden" wire:model="latitude" id="latitude">
                        <input type="hidden" wire:model="longitude" id="longitude">
                    </div>

                    @if ($mode === 'reporting-lost' || $mode === 'searching')
                        <div class="col-span-6 sm:col-span-3">
                            <label for="date_lost" class="block text-sm font-medium text-gray-700">Date Lost</label>
                            <x-input type="date" wire:model="date_lost" id="date_lost" class="mt-1 block w-full" />
                            <x-input-error for="date_lost" class="mt-2" />
                        </div>
                    @elseif ($mode === 'reporting-found')
                        <div class="col-span-6 sm:col-span-3">
                            <label for="date_found" class="block text-sm font-medium text-gray-700">Date Found</label>
                            <x-input type="date" wire:model="date_found" id="date_found" class="mt-1 block w-full" />
                            <x-input-error for="date_found" class="mt-2" />
                        </div>
                    @endif
                </x-slot>

                <x-slot name="actions">
                    <x-button type="submit" data-tippy-content="Next">
                        <i class="fas fa-arrow-right"></i>
                    </x-button>
                </x-slot>
            </x-form-section>
        </div>
@endif
    <!-- Add Category Modal -->
    <x-dialog-modal wire:model.live="showCategoryModal">
        <x-slot name="title">
            {{ __('Add Category') }}
        </x-slot>

        <x-slot name="content">
            <div class="mt-4">
                <x-label for="newCategoryName" value="{{ __('Category Name') }}" />
                <x-input id="newCategoryName" type="text" class="mt-1 block w-full"
                    wire:model.defer="newCategoryName" />
                <x-input-error for="newCategoryName" class="mt-2" />
            </div>
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$set('showCategoryModal', false)">
                {{ __('Cancel') }}
            </x-secondary-button>

            <!-- Add type="button" to prevent form submission -->
            <x-button type="button" class="ms-3" wire:click="saveCategory">
                {{ __('Save') }}
            </x-button>
        </x-slot>
    </x-dialog-modal>

    <!-- Step 2: Item Details -->
    @if ($currentStep == 2)
        <div>
            <x-form-section submit="step2" class="space-y-6 p-6 rounded-lg">
                <x-slot name="title">
                    {{ __('Item Details') }}
                </x-slot>

                <x-slot name="description">
                    {{ __('Provide additional details about the item.') }}
                </x-slot>

                <x-slot name="form">
                    <div class="col-span-6 sm:col-span-3">
                        <label for="condition" class="block text-sm font-medium text-gray-700">Condition</label>
                        <x-input type="text" wire:model="condition" id="condition" class="mt-1 block w-full" />
                        <x-input-error for="condition" class="mt-2" />
                    </div>

                    @if ($mode === 'searching')
                        <div class="col-span-6 sm:col-span-3">
                            <label for="value" class="block text-sm font-medium text-gray-700">Estimated
                                Value</label>
                            <x-input type="number" wire:model="value" id="value" class="mt-1 block w-full" />
                            <x-input-error for="value" class="mt-2" />
                        </div>
                    @endif

                    <div class="col-span-6 sm:col-span-3">
                        <label for="images" class="block text-sm font-medium text-gray-700">Upload images</label>
                        <x-filepond::upload wire:model="images" max-files="5" class="mt-1 block w-full" />
                        <x-input-error for="images" class="mt-2" />
                    </div>
                </x-slot>

                <x-slot name="actions">
                    <div class="flex justify-between w-full">
                        <!-- Back Button -->
                        <x-info-button type="button" wire:click="back(1)" data-tippy-content="Back"
                            class="flex items-center space-x-2">
                            <i class="fas fa-arrow-left"></i>
                            <span>Back</span>
                        </x-info-button>

                        <!-- Next Button -->
                        <x-button type="submit" data-tippy-content="Next" class="flex items-center space-x-2">
                            <span>Next</span>
                            <i class="fas fa-arrow-right"></i>
                        </x-button>
                    </div>
                </x-slot>
            </x-form-section>
        </div>
    @endif


    <!-- Step 3: Additional Information -->
    @if ($currentStep == 3)
        <div>
            <x-form-section submit="step3" class="space-y-6 p-6 rounded-lg">
                <x-slot name="title">
                    {{ __('Additional Information') }}
                </x-slot>

                <x-slot name="description">
                    @if ($mode === 'reporting-lost')
                        {{ __('Provide any additional information about the lost item.') }}
                    @else
                        {{ __('Continue to confirmation.') }}
                    @endif
                </x-slot>

                <x-slot name="form">
                    @if ($mode === 'reporting-lost')
                        <!-- Show the "Report Anonymously" checkbox only for reporting lost mode -->
                        <div class="col-span-6 sm:col-span-3">
                            <label for="is_anonymous" class="flex items-center">
                                <x-input type="checkbox" wire:model="is_anonymous" id="is_anonymous"
                                    class="w-5 h-5 rounded border-gray-300 text-blue-600 focus:ring-blue-500" />
                                <span class="ml-2 text-sm text-gray-700">Report Anonymously</span>
                            </label>
                        </div>
                    @else
                        <!-- Display a message for searching and reporting found modes -->
                        <div class="col-span-6 sm:col-span-3">
                            <p class="text-gray-600">
                                No additional information is required for this mode. Click "Next" to proceed to
                                confirmation.
                            </p>
                        </div>
                    @endif
                </x-slot>

                <x-slot name="actions">
                    <div class="flex justify-between w-full">
                        <!-- Back Button -->
                        <x-info-button type="button" wire:click="back(2)" data-tippy-content="Back"
                            class="flex items-center space-x-2">
                            <i class="fas fa-arrow-left"></i>
                            <span>Back</span>
                        </x-info-button>

                        <!-- Next Button -->
                        <x-button type="submit" data-tippy-content="Next" class="flex items-center space-x-2">
                            <span>Next</span>
                            <i class="fas fa-arrow-right"></i>
                        </x-button>
                    </div>
                </x-slot>
            </x-form-section>
        </div>
    @endif

    <!-- Step 4: Confirmation -->
    @if ($currentStep == 4)
        <div x-data="{ submitting: false }" x-init="@this.on('submitted', () => {
            submitting = true;
            setTimeout(() => { submitting = false }, 15000);
        })">
            <!-- Heading -->
            <h2 class="text-3xl font-bold mb-8 text-gray-800 text-center">Confirmation</h2>

            <!-- Uploaded Images Section -->
            @if ($images && count($images) > 0)
                <div class="mb-8">
                    <h3 class="text-xl font-semibold text-gray-700 mb-4 text-center">Uploaded Images</h3>
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                        @foreach ($images as $image)
                            <div
                                class="relative group overflow-hidden rounded-full w-24 h-24 mx-auto transform transition-transform duration-300 hover:scale-110">
                                <img src="{{ $image->temporaryUrl() }}" alt="Uploaded Image"
                                    class="w-full h-full object-cover rounded-full border-2 border-gray-200">
                                <div
                                    class="absolute inset-0 bg-black bg-opacity-30 opacity-0 group-hover:opacity-100 transition-opacity duration-300 rounded-full">
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Confirmation Details Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                <!-- Box 1: Basic Information -->
                <div class="bg-white p-6 rounded-lg shadow-md border border-gray-100">
                    <h3 class="text-xl font-semibold text-gray-700 mb-4">Basic Information</h3>
                    <div class="space-y-3">
                        <p><strong class="text-gray-600">Title:</strong> <span
                                class="text-gray-800">{{ $title }}</span></p>
                        <p><strong class="text-gray-600">Description:</strong> <span
                                class="text-gray-800">{{ $description }}</span></p>
                    </div>
                </div>

                <!-- Box 2: Category and Location -->
                <div class="bg-white p-6 rounded-lg shadow-md border border-gray-100">
                    <h3 class="text-xl font-semibold text-gray-700 mb-4">Location & Category</h3>
                    <div class="space-y-3">
                        <p><strong class="text-gray-600">Category:</strong> <span
                                class="text-gray-800">{{ $category_name }}</span></p>
                        <p><strong class="text-gray-600">Location:</strong> <span
                                class="text-gray-800">{{ $location }}</span></p>
                    </div>
                </div>

                <!-- Box 3: Additional Details -->
                <div class="bg-white p-6 rounded-lg shadow-md border border-gray-100">
                    <h3 class="text-xl font-semibold text-gray-700 mb-4">Additional Details</h3>
                    <div class="space-y-3">
                        @if ($mode === 'searching')
                            <p><strong class="text-gray-600">Date Lost:</strong> <span
                                    class="text-gray-800">{{ $date_lost }}</span></p>
                            <p><strong class="text-gray-600">Estimated Value:</strong> <span
                                    class="text-gray-800">{{ $value }}</span></p>
                        @elseif ($mode === 'reporting-found')
                            <p><strong class="text-gray-600">Date Found:</strong> <span
                                    class="text-gray-800">{{ $date_found }}</span></p>
                        @endif
                        <p><strong class="text-gray-600">Condition:</strong> <span
                                class="text-gray-800">{{ $condition }}</span></p>
                        @if ($mode === 'reporting-lost')
                            <p><strong class="text-gray-600">Anonymous:</strong> <span
                                    class="text-gray-800">{{ $is_anonymous ? 'Yes' : 'No' }}</span></p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-between mt-6">
                <x-info-button wire:click="back(3)" data-tippy-content="Back" class="flex items-center space-x-2">
                    <i class="fas fa-arrow-left"></i>
                    <span>Back</span>
                </x-info-button>
                <x-button type="submit" wire:click="submit" @click="submitting = true"
                    class="px-6 py-3 rounded-lg bg-blue-600 hover:bg-blue-700 text-white flex items-center space-x-2">
                    <i class="fas fa-check"></i>
                    <span>Submit</span>
                </x-button>
            </div>

            <!-- Submitting Animation -->
            <div x-show="submitting" x-transition:enter="transition ease-out duration-500"
                x-transition:leave="transition ease-in duration-500 delay-500"
                class="fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-90 z-50">
                <div class="bg-white p-8 rounded-xl shadow-2xl text-center max-w-md mx-4">
                    <!-- Animated Checkmark Icon -->
                    <div class="animate-bounce">
                        <i class="fas fa-check-circle text-green-500 text-6xl mb-6"></i>
                    </div>

                    <!-- Heading -->
                    <h2 class="text-2xl font-bold text-gray-800 mb-4">Item
                        {{ $mode === 'reporting-lost' ? 'Reported' : ($mode === 'searching' ? 'Searched' : 'Found') }}
                        Successfully</h2>

                    <!-- Description -->
                    <p class="text-gray-600 mb-6">
                        Thank you for
                        {{ $mode === 'reporting-lost' ? 'reporting' : ($mode === 'searching' ? 'searching for' : 'reporting found') }}
                        the item. Your
                        contribution helps us maintain a safe and organized community.
                        We will notify you as soon as your item is found.
                    </p>

                    <!-- Loading Spinner -->
                    <div class="flex justify-center items-center space-x-2">
                        <div class="w-4 h-4 bg-green-500 rounded-full animate-pulse"></div>
                        <div class="w-4 h-4 bg-green-500 rounded-full animate-pulse delay-150"></div>
                        <div class="w-4 h-4 bg-green-500 rounded-full animate-pulse delay-300"></div>
                    </div>

                    <!-- Additional Message -->
                    <p class="text-sm text-gray-500 mt-6">
                        You will be redirected shortly...
                    </p>
                </div>
            </div>
        </div>
    @endif
    <x-section-border />
</div>
@script
<script>
    document.addEventListener('livewire:load', () => {
        var map = L.map('map').setView([51.505, -0.09], 13);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        var marker = L.marker([51.5, -0.09]).addTo(map);

        map.on('click', function(e) {
            var coordinates = e.latlng;
            document.getElementById('latitude').value = coordinates.lat;
            document.getElementById('longitude').value = coordinates.lng;
            marker.setLatLng(coordinates);
        });

        Livewire.on('morph.updated', () => {
            map.invalidateSize();
        });
    });
</script>
@endscript