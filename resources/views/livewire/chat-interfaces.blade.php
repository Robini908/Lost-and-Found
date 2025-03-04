<div x-data="{ isChatOpen: false }"
     x-init="
        tippy('[data-tippy-content]', {
            theme: 'chat-theme',
            animation: 'scale',
            placement: 'left'
        })
     "
     class="fixed bottom-20 right-4 z-[100]">
    <!-- Chat Toggle Button -->
    <button
        @click="isChatOpen = !isChatOpen"
        data-tippy-content="Chat with AI Assistant"
        class="flex items-center gap-2 bg-gradient-to-r from-emerald-500 to-teal-500 text-white p-4 rounded-full shadow-lg hover:from-emerald-600 hover:to-teal-600 transition-all duration-300 transform hover:scale-105 group"
    >
        <span x-show="!isChatOpen" class="flex items-center">
            <i class="far fa-comments text-xl mr-2 group-hover:animate-bounce"></i>
            <span class="font-medium">Chat Assistant</span>
        </span>
        <i x-show="isChatOpen" class="fas fa-times text-xl"></i>
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
        class="fixed bottom-28 right-4 w-96 bg-white rounded-2xl shadow-2xl border border-gray-100"
        style="max-height: 80vh;"
    >
        <!-- Chat Header -->
        <div class="bg-gradient-to-r from-emerald-500 to-teal-500 p-4 sticky top-0 z-10">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center">
                    <i class="fas fa-robot text-xl text-white"></i>
                </div>
                <div class="flex-1">
                    <h2 class="text-lg font-semibold text-white flex items-center gap-2">
                        AI Assistant
                        <span class="flex h-2.5 w-2.5">
                            <span class="animate-ping absolute inline-flex h-2.5 w-2.5 rounded-full bg-emerald-300 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-400"></span>
                        </span>
                    </h2>
                    <p class="text-sm text-white/80 flex items-center gap-1">
                        <i class="fas fa-circle text-[8px] text-emerald-300"></i>
                        Online
                    </p>
                </div>
                <div class="flex gap-2">
                    @if(app('role-permission')->isAtLeastModerator(auth()->user()))
                        <div class="moderator-controls">
                            <button wire:click="clearAllChats" data-tippy-content="Clear all chat history">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                            <button wire:click="exportChats" data-tippy-content="Export chat logs">
                                <i class="fas fa-download"></i>
                            </button>
                        </div>
                    @endif
                    <button
                        @click="isChatOpen = false"
                        data-tippy-content="Close chat"
                        class="text-white/80 hover:text-white transition-colors p-2 rounded-full hover:bg-white/10"
                    >
                        <i class="fas fa-times"></i>
            </button>
                </div>
            </div>
        </div>

        <!-- Chat Messages -->
        <div class="bg-gray-50 overflow-y-auto p-4 space-y-4" id="chat-messages" style="height: calc(80vh - 160px);">
            @foreach($messages as $message)
                <div class="flex {{ $message['sender'] === 'user' ? 'justify-end' : 'justify-start' }} items-start gap-2">
                    @if($message['sender'] === 'bot')
                        <div class="w-8 h-8 rounded-full bg-gradient-to-r from-emerald-500 to-teal-500 flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-robot text-sm text-white"></i>
                        </div>
                    @endif

                    <div class="{{ $message['sender'] === 'user' ? 'bg-gradient-to-r from-emerald-50 to-teal-50' : 'bg-white' }} rounded-2xl p-4 shadow-sm max-w-[80%] space-y-2 border border-gray-100">
                        <div class="prose prose-sm max-w-none break-words">
                            {!! nl2br(e($message['text'])) !!}
                        </div>
                    </div>

                    @if($message['sender'] === 'user')
                        <div class="w-8 h-8 rounded-full bg-gradient-to-r from-emerald-100 to-teal-100 flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-user text-sm text-emerald-600"></i>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        <!-- Chat Input -->
        <div class="border-t border-gray-100 p-4 bg-white sticky bottom-0 z-10">
            <form wire:submit.prevent="sendMessage" class="flex items-center gap-2">
                <div class="relative flex-1">
                    <input
                        type="text"
                        wire:model="newMessage"
                        placeholder="Type your message..."
                        class="w-full px-4 py-3 pl-10 border border-gray-200 rounded-xl focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 bg-gray-50"
                        @keydown.enter="$event.preventDefault()"
                        autocomplete="off"
                        data-tippy-content="Press Enter to send"
                    />
                    <i class="far fa-comment absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                </div>
                <div class="flex gap-2">
                    <button
                        type="button"
                        data-tippy-content="Add emoji"
                        class="text-gray-400 hover:text-gray-600 p-2 rounded-full hover:bg-gray-100 transition-colors"
                    >
                        <i class="far fa-smile text-lg"></i>
                    </button>
                    <button
                        type="submit"
                        data-tippy-content="Send message"
                        class="bg-gradient-to-r from-emerald-500 to-teal-500 text-white p-3 rounded-xl hover:from-emerald-600 hover:to-teal-600 transition-all duration-200 flex items-center justify-center min-w-[44px] disabled:opacity-50"
                        wire:loading.class="opacity-50 cursor-not-allowed"
                    >
                        <span wire:loading.remove>
                            <i class="far fa-paper-plane text-lg"></i>
                        </span>
                        <span wire:loading>
                            <i class="fas fa-spinner fa-spin text-lg"></i>
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Add custom Tippy theme -->
    <style>
        .tippy-box[data-theme~='chat-theme'] {
            background-color: #064e3b;
            color: white;
        }
        .tippy-box[data-theme~='chat-theme'][data-placement^='top'] > .tippy-arrow::before {
            border-top-color: #064e3b;
        }
        .tippy-box[data-theme~='chat-theme'][data-placement^='bottom'] > .tippy-arrow::before {
            border-bottom-color: #064e3b;
        }
        .tippy-box[data-theme~='chat-theme'][data-placement^='left'] > .tippy-arrow::before {
            border-left-color: #064e3b;
        }
        .tippy-box[data-theme~='chat-theme'][data-placement^='right'] > .tippy-arrow::before {
            border-right-color: #064e3b;
        }
    </style>

    <!-- JavaScript for Auto-scroll -->
    <script>
        document.addEventListener('livewire:initialized', () => {
            const chatMessages = document.getElementById('chat-messages');

            function scrollToBottom() {
                chatMessages.scrollTop = chatMessages.scrollHeight;
            }

            @this.on('messageSent', () => {
                setTimeout(scrollToBottom, 100);
            });

            scrollToBottom();
        });
    </script>
</div>
