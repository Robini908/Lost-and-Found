<x-guest-layout>
    <x-slot name="title">How It Works</x-slot>

    <div class="min-h-screen bg-gradient-to-b from-blue-50 to-white">
        <!-- Hero Section -->
        <div class="pt-32 pb-16 text-center">
            <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-6">
                How Our Lost & Found System Works
            </h1>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto px-6">
                Our AI-powered platform makes finding and returning lost items easier than ever.
            </p>
        </div>

        <!-- Process Steps -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-24">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-12">
                <!-- Step 1 -->
                <div class="relative" x-data="{ isHovered: false }">
                    <div class="bg-white rounded-2xl p-8 shadow-lg transition-all duration-300 hover:shadow-2xl"
                         @mouseenter="isHovered = true"
                         @mouseleave="isHovered = false">
                        <div class="absolute -top-6 left-1/2 transform -translate-x-1/2">
                            <div class="w-12 h-12 bg-blue-600 rounded-full flex items-center justify-center text-white text-xl font-bold">
                                1
                            </div>
                        </div>
                        <div class="mt-6">
                            <h3 class="text-xl font-semibold text-gray-900 mb-4 text-center">Report Your Item</h3>
                            <p class="text-gray-600">
                                Submit a detailed report about your lost item or an item you've found. Include photos, location, and any distinguishing features.
                            </p>
                            <div class="mt-6 text-center">
                                <a href="{{ route('report-item') }}" class="inline-flex items-center text-blue-600 hover:text-blue-700">
                                    Report Now
                                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 2 -->
                <div class="relative" x-data="{ isHovered: false }">
                    <div class="bg-white rounded-2xl p-8 shadow-lg transition-all duration-300 hover:shadow-2xl"
                         @mouseenter="isHovered = true"
                         @mouseleave="isHovered = false">
                        <div class="absolute -top-6 left-1/2 transform -translate-x-1/2">
                            <div class="w-12 h-12 bg-green-600 rounded-full flex items-center justify-center text-white text-xl font-bold">
                                2
                            </div>
                        </div>
                        <div class="mt-6">
                            <h3 class="text-xl font-semibold text-gray-900 mb-4 text-center">AI Matching</h3>
                            <p class="text-gray-600">
                                Our advanced AI system analyzes reports, photos, and locations to find potential matches between lost and found items.
                            </p>
                            <div class="mt-6 text-center">
                                <span class="inline-flex items-center text-green-600">
                                    <i class="fas fa-robot mr-2"></i>
                                    Automated Matching
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 3 -->
                <div class="relative" x-data="{ isHovered: false }">
                    <div class="bg-white rounded-2xl p-8 shadow-lg transition-all duration-300 hover:shadow-2xl"
                         @mouseenter="isHovered = true"
                         @mouseleave="isHovered = false">
                        <div class="absolute -top-6 left-1/2 transform -translate-x-1/2">
                            <div class="w-12 h-12 bg-purple-600 rounded-full flex items-center justify-center text-white text-xl font-bold">
                                3
                            </div>
                        </div>
                        <div class="mt-6">
                            <h3 class="text-xl font-semibold text-gray-900 mb-4 text-center">Secure Recovery</h3>
                            <p class="text-gray-600">
                                When a match is found, we facilitate secure communication between parties and guide you through the recovery process.
                            </p>
                            <div class="mt-6 text-center">
                                <span class="inline-flex items-center text-purple-600">
                                    <i class="fas fa-shield-alt mr-2"></i>
                                    Safe & Secure
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
