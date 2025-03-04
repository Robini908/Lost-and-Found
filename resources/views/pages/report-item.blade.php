<x-guest-layout>
    <x-slot name="title">Report Item</x-slot>

    <div class="min-h-screen bg-gradient-to-b from-blue-50 to-white">
        <!-- Hero Section -->
        <div class="pt-32 pb-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <h1 class="text-4xl md:text-5xl font-bold text-gray-900 text-center mb-6">
                    Report an Item
                </h1>
                <p class="text-xl text-gray-600 text-center max-w-3xl mx-auto">
                    Choose whether you want to report a lost item or a found item.
                </p>
            </div>
        </div>

        <!-- Options Grid -->
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 pb-24">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Lost Item Card -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden transition-all duration-300 hover:shadow-2xl">
                    <div class="p-8">
                        <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mb-6">
                            <i class="fas fa-search text-2xl text-blue-600"></i>
                        </div>
                        <h3 class="text-2xl font-semibold text-gray-900 mb-4">
                            Lost an Item?
                        </h3>
                        <p class="text-gray-600 mb-6">
                            Report your lost item and we'll help you find it. Our AI system will match it with found items in our database.
                        </p>
                        <a href="{{ route('products.report-item') }}"
                           class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 transition duration-150 w-full">
                            Report Lost Item
                        </a>
                    </div>
                </div>

                <!-- Found Item Card -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden transition-all duration-300 hover:shadow-2xl">
                    <div class="p-8">
                        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mb-6">
                            <i class="fas fa-hand-holding-heart text-2xl text-green-600"></i>
                        </div>
                        <h3 class="text-2xl font-semibold text-gray-900 mb-4">
                            Found an Item?
                        </h3>
                        <p class="text-gray-600 mb-6">
                            Help someone recover their lost item by reporting it. You'll be notified when a match is found.
                        </p>
                        <a href="{{ route('products.report-found-item') }}"
                           class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-lg text-white bg-green-600 hover:bg-green-700 transition duration-150 w-full">
                            Report Found Item
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
