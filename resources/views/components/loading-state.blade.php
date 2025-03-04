@props(['message' => 'Loading...'])

<div x-cloak x-show="loading" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="loading-modal" role="dialog" aria-modal="true">
    <!-- Background overlay -->
    <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity backdrop-blur-sm"></div>

    <!-- Loading content -->
    <div class="flex items-center justify-center min-h-screen">
        <div class="bg-white rounded-2xl p-6 sm:p-8 shadow-xl transform transition-all max-w-sm w-full mx-4 relative">
            <!-- Pulsing border effect -->
            <div class="absolute inset-0 rounded-2xl bg-gradient-to-r from-blue-500 to-indigo-500 animate-pulse opacity-20"></div>

            <div class="relative">
                <!-- Spinner -->
                <div class="flex justify-center">
                    <div class="relative">
                        <!-- Outer ring -->
                        <div class="w-16 h-16 border-4 border-blue-100 rounded-full animate-spin"></div>
                        <!-- Inner ring -->
                        <div class="w-16 h-16 border-4 border-blue-600 rounded-full animate-spin absolute top-0 border-t-transparent"></div>
                        <!-- Center dot -->
                        <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-4 h-4 bg-blue-600 rounded-full"></div>
                    </div>
                </div>

                <!-- Message -->
                <div class="mt-4 text-center">
                    <h3 class="text-lg font-medium text-gray-900 mb-1">{{ $message }}</h3>
                    <p class="text-sm text-gray-500">Please wait while we process your request...</p>
                </div>
            </div>
        </div>
    </div>
</div>
