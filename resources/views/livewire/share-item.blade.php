<div>
    <!-- Share Button -->
    <button wire:click="openModal"
            class="flex items-center justify-center w-full px-4 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl hover:from-blue-700 hover:to-indigo-700 transform hover:scale-[1.02] transition-all duration-200">
        <i class="fas fa-share-alt mr-2"></i>
        Share Item
    </button>

    <!-- Share Modal -->
    <div x-data="{
            activeTab: @entangle('activeTab'),
            showModal: @entangle('showModal'),
            copied: false,
            copyText(text) {
                navigator.clipboard.writeText(text);
                this.copied = true;
                setTimeout(() => this.copied = false, 2000);
            }
         }"
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
                 class="relative transform overflow-hidden rounded-2xl bg-white shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-xl">

                <!-- Modal header -->
                <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-4">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-medium text-white">
                            Share "{{ $shareTitle }}"
                        </h3>
                        <button wire:click="closeModal" class="text-white hover:text-gray-200 focus:outline-none">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>

                <!-- Tabs -->
                <div class="border-b border-gray-200">
                    <nav class="flex -mb-px" aria-label="Tabs">
                        <button @click="activeTab = 'social'"
                                :class="{'border-blue-500 text-blue-600': activeTab === 'social',
                                        'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'social'}"
                                class="w-1/3 py-4 px-1 text-center border-b-2 font-medium text-sm transition-colors duration-200">
                            <i class="fas fa-share-alt mr-2"></i>
                            Social
                        </button>
                        <button @click="activeTab = 'qr'"
                                :class="{'border-blue-500 text-blue-600': activeTab === 'qr',
                                        'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'qr'}"
                                class="w-1/3 py-4 px-1 text-center border-b-2 font-medium text-sm transition-colors duration-200">
                            <i class="fas fa-qrcode mr-2"></i>
                            QR Code
                        </button>
                        <button @click="activeTab = 'embed'"
                                :class="{'border-blue-500 text-blue-600': activeTab === 'embed',
                                        'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'embed'}"
                                class="w-1/3 py-4 px-1 text-center border-b-2 font-medium text-sm transition-colors duration-200">
                            <i class="fas fa-code mr-2"></i>
                            Embed
                        </button>
                    </nav>
                </div>

                <!-- Tab panels -->
                <div class="p-6">
                    <!-- Social sharing panel -->
                    <div x-show="activeTab === 'social'" class="space-y-6">
                        <!-- Social media buttons -->
                        <div class="grid grid-cols-2 gap-4">
                            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode($shareUrl) }}"
                               target="_blank"
                               class="flex items-center justify-center px-4 py-3 bg-[#1877f2] text-white rounded-xl hover:bg-[#166fe5] transform hover:scale-[1.02] transition-all duration-200">
                                <i class="fab fa-facebook-f text-lg mr-3"></i>
                                Facebook
                            </a>
                            <a href="https://twitter.com/intent/tweet?url={{ urlencode($shareUrl) }}&text={{ urlencode($shareTitle) }}"
                               target="_blank"
                               class="flex items-center justify-center px-4 py-3 bg-[#1da1f2] text-white rounded-xl hover:bg-[#1a91da] transform hover:scale-[1.02] transition-all duration-200">
                                <i class="fab fa-twitter text-lg mr-3"></i>
                                Twitter
                            </a>
                            <a href="https://wa.me/?text={{ urlencode($shareTitle . ' - ' . $shareUrl) }}"
                               target="_blank"
                               class="flex items-center justify-center px-4 py-3 bg-[#25d366] text-white rounded-xl hover:bg-[#22c35e] transform hover:scale-[1.02] transition-all duration-200">
                                <i class="fab fa-whatsapp text-lg mr-3"></i>
                                WhatsApp
                            </a>
                            <a href="https://telegram.me/share/url?url={{ urlencode($shareUrl) }}&text={{ urlencode($shareTitle) }}"
                               target="_blank"
                               class="flex items-center justify-center px-4 py-3 bg-[#0088cc] text-white rounded-xl hover:bg-[#007ab8] transform hover:scale-[1.02] transition-all duration-200">
                                <i class="fab fa-telegram text-lg mr-3"></i>
                                Telegram
                            </a>
                        </div>

                        <!-- Direct link -->
                        <div class="mt-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Direct Link</label>
                            <div class="flex items-center">
                                <input type="text"
                                       readonly
                                       value="{{ $shareUrl }}"
                                       class="flex-1 block w-full rounded-l-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                <button @click="copyText('{{ $shareUrl }}')"
                                        class="inline-flex items-center px-4 py-2 border border-l-0 border-gray-300 rounded-r-xl bg-gray-50 hover:bg-gray-100 text-sm font-medium text-gray-700 transition-colors duration-200">
                                    <template x-if="!copied">
                                        <i class="fas fa-copy mr-2"></i>
                                    </template>
                                    <template x-if="copied">
                                        <i class="fas fa-check text-green-500 mr-2"></i>
                                    </template>
                                    <span x-text="copied ? 'Copied!' : 'Copy'"></span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- QR Code panel -->
                    <div x-show="activeTab === 'qr'" class="text-center space-y-6">
                        <div class="bg-white p-4 rounded-xl inline-block mx-auto">
                            <img src="data:image/svg+xml;base64,{{ $qrCode }}"
                                 alt="QR Code"
                                 class="w-48 h-48 mx-auto">
                        </div>
                        <button wire:click="downloadQrCode"
                                class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-xl bg-white hover:bg-gray-50 text-sm font-medium text-gray-700 transition-colors duration-200">
                            <i class="fas fa-download mr-2"></i>
                            Download QR Code
                        </button>
                    </div>

                    <!-- Embed panel -->
                    <div x-show="activeTab === 'embed'" class="space-y-4">
                        <div class="bg-gray-50 p-4 rounded-xl">
                            <pre class="text-sm text-gray-600 whitespace-pre-wrap">{{ $embedCode }}</pre>
                        </div>
                        <button @click="copyText(`{{ $embedCode }}`)"
                                class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-xl bg-white hover:bg-gray-50 text-sm font-medium text-gray-700 transition-colors duration-200">
                            <template x-if="!copied">
                                <i class="fas fa-copy mr-2"></i>
                            </template>
                            <template x-if="copied">
                                <i class="fas fa-check text-green-500 mr-2"></i>
                            </template>
                            <span x-text="copied ? 'Copied!' : 'Copy Embed Code'"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
