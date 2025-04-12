<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Items Management') }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('admin.items.export', 'pdf') }}" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-800 focus:outline-none focus:border-red-800 focus:ring focus:ring-red-300 disabled:opacity-25 transition">
                    <i class="fas fa-file-pdf mr-2"></i> Export All PDF
                </a>
                <a href="{{ route('admin.items.export', 'word') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-800 focus:outline-none focus:border-blue-800 focus:ring focus:ring-blue-300 disabled:opacity-25 transition">
                    <i class="fas fa-file-word mr-2"></i> Export All Word
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <div class="mb-6 flex justify-between items-center">
                    <h3 class="text-lg font-medium text-gray-900">
                        {{ __('All Lost & Found Items') }}
                    </h3>
                    <p class="text-sm text-gray-500">
                        {{ __('Manage all reported lost and found items from here') }}
                    </p>
                </div>

                <livewire:display-lost-items />
            </div>
        </div>
    </div>
</x-app-layout>
