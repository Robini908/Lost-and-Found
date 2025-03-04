@props(['show' => false])

<div x-data="{ show: @entangle($attributes->wire('model')) }"
     x-show="show"
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto"
     x-transition:enter="ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0">

    <!-- Backdrop -->
    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

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
                 class="relative transform rounded-lg bg-white shadow-xl transition-all w-full max-w-6xl">

                <!-- Modal Content -->
                <div class="flex h-[80vh]">
                    <!-- Left Side - Image Gallery -->
                    <div class="w-1/2 bg-gray-900 relative">
                        <div class="h-full">
                            {{ $gallery ?? '' }}
                        </div>
                    </div>

                    <!-- Right Side - Content -->
                    <div class="w-1/2 bg-white">
                        <div class="h-full flex flex-col">
                            <!-- Header -->
                            <div class="flex-none p-6 border-b border-gray-200">
                                {{ $header ?? '' }}
                            </div>

                            <!-- Body -->
                            <div class="flex-1 overflow-y-auto p-6">
                                {{ $content ?? '' }}
                            </div>

                            <!-- Footer -->
                            <div class="flex-none p-6 border-t border-gray-200">
                                {{ $footer ?? '' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
