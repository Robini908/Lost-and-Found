@props(['showContactModal', 'contactMessage'])

<div x-data="{
    show: @entangle('showContactModal'),
    isPreview: false,
    messagePreview: '',
    previewMessage() {
        this.messagePreview = document.getElementById('messageInput').value;
        this.isPreview = true;
    },
    editMessage() {
        this.isPreview = false;
    }
}"
     x-show="show"
     x-cloak
     class="relative z-50"
     aria-labelledby="modal-title"
     role="dialog"
     aria-modal="true">

    <!-- Background backdrop -->
    <div x-show="show"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

    <div class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div x-show="show"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="relative transform overflow-hidden rounded-lg bg-white px-4 pb-4 pt-5 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:p-6">

                <!-- Modal header -->
                <div class="mb-4 sm:flex sm:items-start">
                    <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                        <i class="fas fa-envelope text-blue-600"></i>
                    </div>
                    <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                        <h3 class="text-lg font-semibold leading-6 text-gray-900" id="modal-title">
                            Contact Item Finder
                        </h3>
                        <p class="mt-2 text-sm text-gray-500">
                            Send a message to the person who found this item. They will receive your contact details to respond.
                        </p>
                    </div>
                </div>

                <!-- Founder Information -->
                @if($selectedMatch && $selectedMatch['found_item'])
                    <div class="mb-4 bg-gray-50 rounded-lg p-4">
                        <h4 class="text-sm font-medium text-gray-900 mb-3">Finder Details</h4>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <div class="flex items-center text-sm">
                                    <i class="fas fa-user text-blue-500 w-5"></i>
                                    <span class="font-medium mr-2">Name:</span>
                                    <span class="text-gray-600">{{ $selectedMatch['found_item']->user->name }}</span>
                                </div>
                                <div class="flex items-center text-sm">
                                    <i class="fas fa-calendar text-blue-500 w-5"></i>
                                    <span class="font-medium mr-2">Found:</span>
                                    <span class="text-gray-600">{{ $selectedMatch['found_item']->date_found ? $selectedMatch['found_item']->date_found->format('M d, Y') : 'Not specified' }}</span>
                                </div>
                            </div>
                            <div class="space-y-2">
                                <div class="flex items-center text-sm">
                                    <i class="fas fa-map-marker-alt text-blue-500 w-5"></i>
                                    <span class="font-medium mr-2">Location:</span>
                                    <span class="text-gray-600">{{ $selectedMatch['found_item']->location }}</span>
                                </div>
                                <div class="flex items-center text-sm">
                                    <i class="fas fa-percentage text-blue-500 w-5"></i>
                                    <span class="font-medium mr-2">Match:</span>
                                    <span class="text-gray-600">{{ number_format($selectedMatch['similarity_score'] * 100, 0) }}%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Previous messages section -->
                @if($selectedMatch && $previousMessages = \App\Models\ItemMessage::where('item_id', $selectedMatch['found_item']->id)
                    ->where('from_user_id', auth()->id())
                    ->orderBy('created_at', 'desc')
                    ->get())
                    @if($previousMessages->isNotEmpty())
                        <div class="mb-4 border-t border-b border-gray-100 py-3">
                            <h4 class="text-sm font-medium text-gray-700 mb-2">Previous Messages</h4>
                            <div class="space-y-2 max-h-32 overflow-y-auto">
                                @foreach($previousMessages as $prevMessage)
                                    <div class="text-sm text-gray-600 bg-gray-50 p-2 rounded">
                                        <div class="flex justify-between items-start">
                                            <p class="whitespace-pre-wrap">{{ $prevMessage->message }}</p>
                                            <span class="text-xs text-gray-400 ml-2">{{ $prevMessage->created_at->diffForHumans() }}</span>
                                        </div>
                                        @if($prevMessage->is_sent)
                                            <div class="mt-1 text-xs text-green-600">
                                                <i class="fas fa-check-circle mr-1"></i> Sent
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endif

                <!-- Message form -->
                <form wire:submit.prevent="contactFounder({{ $selectedMatchIndex ?? 'null' }})">
                    <div class="mt-4" x-show="!isPreview">
                        <label for="messageInput" class="block text-sm font-medium text-gray-700">Your Message</label>
                        <div class="mt-1">
                            <textarea
                                wire:model="contactMessage"
                                id="messageInput"
                                name="message"
                                rows="4"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                placeholder="Describe any identifying details or circumstances that can help verify your ownership..."
                                required></textarea>
                        </div>
                        @error('contactMessage')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror

                        <div class="mt-2 flex justify-end">
                            <button
                                type="button"
                                @click="previewMessage"
                                class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <i class="fas fa-eye mr-2"></i>
                                Preview Message
                            </button>
                        </div>
                    </div>

                    <!-- Message Preview -->
                    <div class="mt-4" x-show="isPreview">
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="text-sm font-medium text-gray-900">Message Preview</h4>
                            <button
                                type="button"
                                @click="editMessage"
                                class="text-sm text-blue-600 hover:text-blue-800">
                                <i class="fas fa-edit mr-1"></i>
                                Edit Message
                            </button>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4 whitespace-pre-wrap" x-text="messagePreview"></div>
                    </div>

                    <!-- Action buttons -->
                    <div class="mt-5 sm:mt-6 sm:grid sm:grid-flow-row-dense sm:grid-cols-2 sm:gap-3">
                        <button
                            type="submit"
                            class="inline-flex w-full justify-center rounded-md bg-blue-600 px-3 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 sm:col-start-2"
                            x-bind:disabled="!messagePreview && isPreview">
                            <i class="fas fa-paper-plane mr-2"></i>
                            Send Message
                        </button>
                        <button
                            type="button"
                            class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-medium text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:col-start-1 sm:mt-0"
                            @click="show = false">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
