<div>
    <!-- Report Button -->
    <button wire:click="openModal"
            class="flex items-center justify-center w-full px-4 py-2 bg-red-600 text-white rounded-xl hover:bg-red-700 transform hover:scale-[1.02] transition-all duration-200">
        <i class="fas fa-flag mr-2"></i>
        Report Item
    </button>

    <!-- Report Modal -->
    <div x-data="{ showModal: @entangle('showModal') }"
         x-show="showModal"
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto"
         aria-labelledby="modal-title"
         role="dialog"
         aria-modal="true">

        <!-- Background overlay -->
        <div x-show="showModal"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-gray-500 bg-opacity-75 backdrop-blur-sm transition-opacity"></div>

        <!-- Modal panel -->
        <div class="flex min-h-screen items-center justify-center p-4">
            <div x-show="showModal"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="relative transform overflow-hidden rounded-2xl bg-white shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">

                <!-- Modal header -->
                <div class="bg-gradient-to-r from-red-600 to-red-700 px-6 py-4">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-medium text-white">
                            Report Item
                        </h3>
                        <button wire:click="closeModal" class="text-white hover:text-gray-200 focus:outline-none">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>

                <form wire:submit="submitReport" class="p-6">
                    <!-- Item Preview -->
                    <div class="mb-6 p-4 bg-gray-50 rounded-xl">
                        <div class="flex items-center space-x-4">
                            @if($item->images->first())
                                <img src="{{ asset('storage/' . $item->images->first()->image_path) }}"
                                     alt="{{ $item->title }}"
                                     class="w-16 h-16 rounded-lg object-cover">
                            @else
                                <div class="w-16 h-16 rounded-lg bg-gray-200 flex items-center justify-center">
                                    <i class="fas fa-image text-gray-400 text-2xl"></i>
                                </div>
                            @endif
                            <div>
                                <h4 class="font-medium text-gray-900">{{ $item->title }}</h4>
                                <p class="text-sm text-gray-500">{{ Str::limit($item->description, 100) }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Report Reason -->
                    <div class="mb-6">
                        <label for="reason" class="block text-sm font-medium text-gray-700 mb-2">
                            Reason for Report
                        </label>
                        <select wire:model="reason"
                                id="reason"
                                class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                            <option value="">Select a reason</option>
                            @foreach($reportTypes as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('reason')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div class="mb-6">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                            Detailed Description
                        </label>
                        <div class="relative">
                            <textarea wire:model="description"
                                      id="description"
                                      rows="4"
                                      class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                                      placeholder="Please provide specific details about why you're reporting this item..."></textarea>
                            <div class="absolute bottom-2 right-2 text-xs text-gray-400">
                                {{ strlen($description) }}/1000
                            </div>
                        </div>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Guidelines -->
                    <div class="mb-6 p-4 bg-yellow-50 rounded-xl">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-info-circle text-yellow-400"></i>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-yellow-800">
                                    Reporting Guidelines
                                </h3>
                                <div class="mt-2 text-sm text-yellow-700">
                                    <ul class="list-disc pl-5 space-y-1">
                                        <li>Be specific about why you're reporting this item</li>
                                        <li>Include any relevant details or evidence</li>
                                        <li>Reports are reviewed by our moderation team</li>
                                        <li>False reports may result in account restrictions</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center justify-end space-x-3">
                        <button type="button"
                                wire:click="closeModal"
                                class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-xl bg-white hover:bg-gray-50 text-sm font-medium text-gray-700 transition-colors duration-200">
                            Cancel
                        </button>
                        <button type="submit"
                                class="inline-flex items-center px-4 py-2 border border-transparent rounded-xl bg-red-600 hover:bg-red-700 text-sm font-medium text-white transition-colors duration-200">
                            <i class="fas fa-flag mr-2"></i>
                            Submit Report
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
