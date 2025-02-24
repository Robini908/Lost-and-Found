<div>
    <!-- Table View -->
    @if (!$editingItem)
        <div class="bg-gray-100 shadow-sm rounded-lg p-3 mb-2">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Items you reported to have lost') }}
            </h2>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            <th
                                class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Title
                            </th>
                            <th
                                class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Location
                            </th>
                            <th
                                class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Date Lost
                            </th>
                            <th
                                class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-gray-100 divide-y divide-gray-200">
                        @foreach ($userItems as $item)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $item->title }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $item->location }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <div class="text-sm text-gray-600 mb-1">

                                        <span class="font-semibold">
                                            {{ $item->date_lost ? $item->date_lost->format('F j, Y') : 'Not provided' }}
                                        </span>
                                    </div>                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <button wire:click="loadItem({{ $item->id }})"
                                        class="text-blue-500 hover:text-blue-700" data-tippy-content="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- Edit Form -->
    @if ($editingItem)
        <div class="bg-gray-100  shadow-sm rounded-lg p-4 mb-6">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Edit Lost Item') }}
            </h2>
            <form wire:submit.prevent="saveItem">
                <!-- Two Grid Layout -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Left Grid: Form Fields -->
                    <div class="space-y-6">
                        <!-- Group 1: Basic Details -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Title -->
                            <div>
                                <x-label for="title" value="Title" />
                                <x-input wire:model="title" id="title" class="mt-1 block w-full" />
                                <x-input-error for="title" class="mt-2" />
                            </div>

                            <!-- Location -->
                            <div>
                                <x-label for="location" value="Location" />
                                <x-input wire:model="location" id="location" class="mt-1 block w-full" />
                                <x-input-error for="location" class="mt-2" />
                            </div>
                        </div>

                        <!-- Date Lost -->
                        <div>
                            <!-- Display the Retrieved Date -->
                            <div class="text-sm text-gray-600 mb-1">

                                <span class="font-semibold">
                                    {{ $date_lost ? \Carbon\Carbon::parse($date_lost)->format('F j, Y') : 'Not provided' }}
                                </span>
                            </div>
                            <!-- Date Input Field -->
                            <div>
                                <x-label for="date_lost" value="Date Lost" />
                                <x-input wire:model="date_lost" type="date" id="date_lost"
                                    class="mt-1 block w-full" />
                                <x-input-error for="date_lost" class="mt-2" />
                            </div>
                        </div>

                        <!-- Group 2: Category and Condition -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Category -->
                            <div>
                                <x-label for="category_id" value="Category" />
                                <x-select wire:model.live="category_id" id="category_id" class="mt-1 block w-full">
                                    <option value="">Select Category</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </x-select>
                                <x-input-error for="category_id" class="mt-2" />
                            </div>

                            <!-- Condition -->
                            <div>
                                <!-- Display Retrieved Condition -->
                                <div class="mb-2">
                                    <span class="text-sm text-gray-600">Current Condition:
                                        <strong>{{ $condition }}</strong></span>
                                </div>

                                <!-- Condition Field -->
                                <div>
                                    <x-label for="condition" value="Condition" />
                                    <div class="relative">
                                        <!-- Dropdown -->
                                        <x-select wire:model="condition" id="condition" class="mt-1 block w-full pr-10">
                                            <option value="New">New</option>
                                            <option value="Used">Used</option>
                                            <option value="Damaged">Damaged</option>
                                            <option value="Other">Other (Type Below)</option>
                                        </x-select>
                                        <!-- Input for Custom Value -->
                                        @if ($condition === 'Other')
                                            <x-input wire:model="condition" id="custom_condition"
                                                class="mt-2 block w-full" placeholder="Enter custom condition" />
                                        @endif
                                    </div>
                                    <x-input-error for="condition" class="mt-2" />
                                </div>
                            </div>
                        </div>

                        <!-- Value -->
                        <div>
                            <x-label for="value" value="Value" />
                            <x-input wire:model="value" type="number" id="value" class="mt-1 block w-full" />
                            <x-input-error for="value" class="mt-2" />
                        </div>

                        <!-- Is Anonymous -->
                        <div>
                            <x-label for="is_anonymous" value="Is Anonymous?" />
                            <x-checkbox wire:model="is_anonymous" id="is_anonymous" class="mt-1" />
                            <x-input-error for="is_anonymous" class="mt-2" />
                        </div>

                        <!-- Description (Full Width) -->
                        <div>
                            <x-label for="description" value="Description" />
                            <x-textarea wire:model="description" id="description" class="mt-1 block w-full"
                                rows="4" />
                            <x-input-error for="description" class="mt-2" />
                        </div>
                    </div>


                    <!-- Right Grid: Images -->
                    <div class="space-y-6">
                        <!-- Image Upload and Management -->
                        <div>
                            <x-label for="images" value="Images"
                                class="block text-sm font-medium text-gray-700 mb-2" />

                            <!-- Image Grid -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-2 gap-4">
                                <!-- Existing Images -->
                                <!-- Existing Images -->
                                @foreach ($existingImages as $image)
                                    <div
                                        class="relative group overflow-hidden rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300">
                                        <img src="{{ asset('storage/' . $image->image_path) }}" alt="Lost Item Image"
                                            class="w-full h-32 object-cover rounded-lg transform transition-transform duration-300 group-hover:scale-105">
                                        <!-- Delete Button (for existing images only) -->
                                        <button type="button" wire:click.prevent="deleteImage({{ $image->id }})"
                                            class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 bg-black bg-opacity-50 text-white p-2 rounded-lg transition-opacity duration-300"
                                            data-tippy-content="Delete Image">
                                            <i class="fas fa-trash-alt text-lg"></i>
                                        </button>
                                    </div>
                                @endforeach

                                <!-- New Image Upload -->
                                @foreach ($images as $image)
                                    <div
                                        class="relative group overflow-hidden rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300">
                                        <img src="{{ $image->temporaryUrl() }}" alt="New Image"
                                            class="w-full h-32 object-cover rounded-lg transform transition-transform duration-300 group-hover:scale-105">
                                    </div>
                                @endforeach
                            </div>

                            <!-- FilePond Upload Area -->
                            <div wire:ignore class="mt-4">
                                <x-filepond::upload wire:model="images" id="images" max-files="5" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Save and Cancel Buttons -->
                <div class="flex justify-end space-x-4 mt-6">
                    <button type="button" wire:click="cancelEdit"
                        class="bg-red-500 text-white p-2 rounded-full hover:bg-red-600 transition-colors duration-300"
                        data-tippy-content="Cancel">
                        Cancel
                    </button>
                    <button type="submit"
                        class="bg-green-500 text-white p-2 rounded-full hover:bg-green-600 transition-colors duration-300"
                        data-tippy-content="Save Changes">
                        Save
                    </button>
                </div>
            </form>
        </div>
    @endif
</div>

@script
    <script>
        // Open file picker when the "Add New Image" button is clicked
        Livewire.on('openFilePicker', () => {
            document.getElementById('images').click();
        });
    </script>
@endscript
