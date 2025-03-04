@props(['show' => false])

<div x-data="{
    show: @entangle($attributes->wire('model')),
    activeImage: 0,
    isFullscreen: false,
    totalImages: 0,
    initializeGallery() {
        this.totalImages = document.querySelectorAll('.gallery-image').length;
    }
}"
     x-show="show"
     x-cloak
     x-init="initializeGallery()"
     class="fixed inset-0 z-50 overflow-y-auto"
     x-transition:enter="ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0">

    <!-- Backdrop with blur effect -->
    <div class="fixed inset-0 bg-gray-900/75 backdrop-blur-sm transition-opacity"></div>

    <!-- Fullscreen Image View -->
    <div x-show="isFullscreen"
         x-transition.opacity
         class="fixed inset-0 z-60 bg-black flex items-center justify-center"
         @click.self="isFullscreen = false">
        <button @click="isFullscreen = false"
                class="absolute top-4 right-4 text-white hover:text-gray-300 z-50">
            <i class="fas fa-times text-2xl"></i>
        </button>
        <div class="relative w-full h-full flex items-center justify-center">
            <button @click="activeImage = Math.max(0, activeImage - 1)"
                    class="absolute left-4 p-2 rounded-full bg-white/10 hover:bg-white/20 text-white transition-all duration-200"
                    x-show="activeImage > 0">
                <i class="fas fa-chevron-left text-xl"></i>
            </button>
            <button @click="activeImage = Math.min(totalImages - 1, activeImage + 1)"
                    class="absolute right-4 p-2 rounded-full bg-white/10 hover:bg-white/20 text-white transition-all duration-200"
                    x-show="activeImage < totalImages - 1">
                <i class="fas fa-chevron-right text-xl"></i>
            </button>
            <template x-for="(image, index) in document.querySelectorAll('.gallery-image')" :key="index">
                <img :src="image.src"
                     x-show="activeImage === index"
                     class="max-h-screen max-w-full object-contain"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95">
            </template>
        </div>
    </div>

    <!-- Modal Container -->
    <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 sm:p-0">
            <div x-show="show"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="relative transform rounded-2xl bg-white shadow-2xl transition-all w-full max-w-6xl">

                <div class="flex max-h-[90vh]">
                    <!-- Left Side - Image Gallery -->
                    <div class="w-1/2 bg-gradient-to-br from-gray-900 to-gray-800 relative">
                        <div class="h-full relative">
                            <!-- Main Image Display -->
                            <div class="h-[calc(100%-100px)] relative">
                                {{ $gallery ?? '' }}

                                <!-- Fullscreen Button -->
                                <button @click="isFullscreen = true"
                                        class="absolute top-4 right-4 p-2 rounded-full bg-black/20 hover:bg-black/40 text-white backdrop-blur-sm transition-all duration-200">
                                    <i class="fas fa-expand"></i>
                                </button>
                            </div>

                            <!-- Thumbnail Strip -->
                            <div class="absolute bottom-0 left-0 right-0 h-[100px] bg-black/30 backdrop-blur-sm p-3">
                                <div class="h-full flex space-x-2 overflow-x-auto custom-scrollbar-x">
                                    <template x-for="(image, index) in document.querySelectorAll('.gallery-image')" :key="index">
                                        <button @click="activeImage = index"
                                                :class="{'ring-2 ring-blue-500 ring-offset-2 ring-offset-gray-900': activeImage === index}"
                                                class="flex-none w-20 h-full rounded-lg overflow-hidden transition-all duration-200 hover:opacity-100 filter"
                                                :style="activeImage === index ? 'opacity: 1' : 'opacity: 0.6'">
                                            <img :src="image.src" class="w-full h-full object-cover">
                                        </button>
                                    </template>
                                </div>
                            </div>

                            <!-- Image Counter -->
                            <div class="absolute top-4 left-4 px-3 py-1.5 rounded-full bg-black/20 backdrop-blur-sm text-white text-sm font-medium">
                                <span x-text="activeImage + 1"></span> / <span x-text="totalImages"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Right Side - Content -->
                    <div class="w-1/2 bg-white">
                        <div class="h-full flex flex-col">
                            <!-- Header -->
                            <div class="flex-none p-6 border-b border-gray-100">
                                {{ $header ?? '' }}
                            </div>

                            <!-- Body -->
                            <div class="flex-1 overflow-y-auto p-6 space-y-6 custom-scrollbar">
                                {{ $content ?? '' }}
                            </div>

                            <!-- Footer with glass effect -->
                            <div class="flex-none p-6 border-t border-gray-100 bg-white/80 backdrop-blur-sm">
                                {{ $footer ?? '' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.custom-scrollbar {
    scrollbar-width: thin;
    scrollbar-color: #CBD5E1 transparent;
}

.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
}

.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
    background-color: #CBD5E1;
    border-radius: 3px;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background-color: #94A3B8;
}

.custom-scrollbar-x {
    scrollbar-width: thin;
    scrollbar-color: #CBD5E1 transparent;
}

.custom-scrollbar-x::-webkit-scrollbar {
    height: 6px;
}

.custom-scrollbar-x::-webkit-scrollbar-track {
    background: transparent;
}

.custom-scrollbar-x::-webkit-scrollbar-thumb {
    background-color: #CBD5E1;
    border-radius: 3px;
}

.custom-scrollbar-x::-webkit-scrollbar-thumb:hover {
    background-color: #94A3B8;
}
</style>
