<div>
    <button wire:click="openModal"
            class="flex items-center justify-center w-full px-4 py-2 bg-green-600 text-white rounded-xl hover:bg-green-700 transition-colors duration-200">
        <i class="fas fa-envelope mr-2"></i>
        Contact Reporter
    </button>

    <!-- Contact Modal -->
    <div x-data="{ shown: @entangle('showModal') }"
         x-show="shown"
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto"
         aria-labelledby="modal-title"
         role="dialog"
         aria-modal="true">

        <!-- Background overlay -->
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

        <!-- Modal panel -->
        <div class="flex min-h-screen items-center justify-center p-4">
            <div x-show="shown"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="relative transform overflow-hidden rounded-2xl bg-white shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">

                <!-- Modal content -->
                <div class="bg-white">
                    <!-- Header -->
                    <div class="bg-gradient-to-r from-green-600 to-green-700 px-6 py-4">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-medium text-white" id="modal-title">
                                Contact Item Reporter
                            </h3>
                            <button wire:click="closeModal" class="text-white hover:text-gray-200">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Form -->
                    <div class="px-6 py-4">
                        <form wire:submit.prevent="sendMessage">
                            <!-- Subject -->
                            <div class="mb-4">
                                <label for="subject" class="block text-sm font-medium text-gray-700 mb-1">
                                    Subject <span class="text-red-500">*</span>
                                </label>
                                <input type="text"
                                       id="subject"
                                       wire:model.live="subject"
                                       class="w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 @error('subject') border-red-300 @enderror"
                                       placeholder="Enter subject...">
                                @error('subject')
                                    <p class="mt-1 text-sm text-red-600">
                                        <i class="fas fa-exclamation-circle mr-1"></i>
                                        {{ $message }}
                                    </p>
                                @enderror
                                <div class="mt-1 text-xs text-gray-500">
                                    5-100 characters
                                </div>
                            </div>

                            <!-- Contact Method -->
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Contact Method <span class="text-red-500">*</span>
                                </label>
                                <div class="flex space-x-4">
                                    <label class="flex items-center">
                                        <input type="radio"
                                               wire:model.live="contactMethod"
                                               value="in_app"
                                               class="text-green-600 focus:ring-green-500">
                                        <span class="ml-2 text-sm text-gray-700">In-App Message</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="radio"
                                               wire:model.live="contactMethod"
                                               value="email"
                                               class="text-green-600 focus:ring-green-500">
                                        <span class="ml-2 text-sm text-gray-700">Email</span>
                                    </label>
                                </div>
                                @error('contactMethod')
                                    <p class="mt-1 text-sm text-red-600">
                                        <i class="fas fa-exclamation-circle mr-1"></i>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <!-- Message -->
                            <div class="mb-4">
                                <label for="message" class="block text-sm font-medium text-gray-700 mb-1">
                                    Your Message <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <textarea id="message"
                                              wire:model.live="message"
                                              rows="4"
                                              class="w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 @error('message') border-red-300 @enderror"
                                              placeholder="Write your message here..."></textarea>
                                    <div class="absolute bottom-2 right-2 text-xs text-gray-500">
                                        {{ $messageLength }}/1000
                                    </div>
                                </div>
                                @error('message')
                                    <p class="mt-1 text-sm text-red-600">
                                        <i class="fas fa-exclamation-circle mr-1"></i>
                                        {{ $message }}
                                    </p>
                                @enderror
                                <div class="mt-1 text-xs text-gray-500 flex items-center">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Minimum 20 characters required for a meaningful message
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Footer -->
                    <div class="bg-gray-50 px-6 py-4 flex justify-end space-x-3">
                        <button wire:click="closeModal"
                                type="button"
                                class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            Cancel
                        </button>
                        <button wire:click="sendMessage"
                                wire:loading.attr="disabled"
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 disabled:opacity-50 disabled:cursor-not-allowed">
                            <span wire:loading.remove>Send Message</span>
                            <span wire:loading>
                                <i class="fas fa-spinner fa-spin mr-2"></i>
                                Sending...
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
