<div x-data="{ isChatOpen: false }" class="fixed bottom-4 right-4">
    <!-- Chat Toggle Button -->
    <button
        @click="isChatOpen = !isChatOpen"
        class="bg-blue-500 text-white p-4 rounded-full shadow-lg hover:bg-blue-600 transition duration-300"
    >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
        </svg>
    </button>

    <!-- Chat Box -->
    <div
        x-show="isChatOpen"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-4"
        @click.away="isChatOpen = false"
        class="fixed bottom-20 right-4 w-96 bg-white rounded-lg shadow-lg border border-gray-200 overflow-hidden"
    >
        <!-- Chat Header -->
        <div class="bg-blue-500 text-white p-4 flex justify-between items-center">
            <h2 class="text-lg font-semibold">Chat Support</h2>
            <button @click="isChatOpen = false" class="text-white hover:text-gray-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Chat Messages -->
        <div class="p-4 h-64 overflow-y-auto" x-ref="chatMessages">
            @foreach ($messages as $message)
                <div class="mb-4">
                    <div class="{{ $message['sender'] === 'user' ? 'text-right' : 'text-left' }}">
                        <div class="inline-block max-w-xs px-4 py-2 rounded-lg {{ $message['sender'] === 'user' ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-800' }}">
                            {{ $message['text'] }}
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Chat Input -->
        <div class="border-t border-gray-200 p-4">
            <form wire:submit.prevent="sendMessage">
                <div class="flex items-center">
                    <input
                        type="text"
                        wire:model="newMessage"
                        placeholder="Type a message..."
                        class="flex-1 px-4 py-2 border border-gray-300 rounded-l-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    />
                    <button
                        type="submit"
                        class="bg-blue-500 text-white px-4 py-2 rounded-r-lg hover:bg-blue-600 transition duration-300"
                    >
                        <span wire:loading.remove>Send</span>
                        <span wire:loading><i class="fas fa-spinner fa-spin"></i> Sending...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>