<x-search-layout>
    <x-slot name="header">
        Search Items
    </x-slot>

    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
        <div class="p-6">
            <!-- Page specific content goes here -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Example content -->
                <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Recent Searches</h3>
                    <p class="mt-1 text-sm text-gray-500">Your recent search history will appear here</p>
                </div>

                <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Popular Categories</h3>
                    <p class="mt-1 text-sm text-gray-500">Most searched categories will appear here</p>
                </div>

                <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Search Tips</h3>
                    <p class="mt-1 text-sm text-gray-500">Tips for better search results will appear here</p>
                </div>
            </div>
        </div>
    </div>
</x-search-layout>
