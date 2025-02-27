<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Item Details') }}
            </h2>
            <x-button
                onclick="window.history.back()"
                class="flex items-center space-x-2"
            >
                <i class="fas fa-arrow-left"></i>
                <span>Back to Rewards</span>
            </x-button>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <!-- Title -->
                    <div class="mb-6">
                        <h1 class="text-2xl font-bold text-gray-900">{{ $item->title }}</h1>
                        <p class="text-sm text-gray-500">{{ $item->item_type === 'found' ? 'Found Item' : 'Lost Item' }}</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Left Column -->
                        <div>
                            <!-- Images -->
                            @if ($item->images->isNotEmpty())
                                <div class="mb-6">
                                    <h3 class="text-lg font-medium text-gray-900 mb-3">Images</h3>
                                    <div class="grid grid-cols-2 gap-4">
                                        @foreach ($item->images as $image)
                                            <div class="relative aspect-w-16 aspect-h-9">
                                                <img src="{{ asset('storage/' . $image->image_path) }}"
                                                     alt="{{ $item->title }}"
                                                     class="rounded-lg object-cover w-full h-full">
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <!-- Description -->
                            <div class="mb-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-2">Description</h3>
                                <p class="text-gray-700">{{ $item->description }}</p>
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div class="space-y-6">
                            <!-- Location -->
                            <div class="bg-gray-50 rounded-lg p-4">
                                <h3 class="text-sm font-medium text-gray-500">Location</h3>
                                <p class="mt-1 text-gray-900">{{ $item->location }}</p>
                            </div>

                            <!-- Date Information -->
                            <div class="bg-gray-50 rounded-lg p-4">
                                <h3 class="text-sm font-medium text-gray-500">
                                    {{ $item->item_type === 'found' ? 'Date Found' : 'Date Lost' }}
                                </h3>
                                <p class="mt-1 text-gray-900">
                                    @if($item->item_type === 'found')
                                        {{ $item->date_found ? $item->date_found->format('F j, Y') : 'Not specified' }}
                                    @else
                                        {{ $item->date_lost ? $item->date_lost->format('F j, Y') : 'Not specified' }}
                                    @endif
                                </p>
                            </div>

                            <!-- Condition -->
                            <div class="bg-gray-50 rounded-lg p-4">
                                <h3 class="text-sm font-medium text-gray-500">Condition</h3>
                                <p class="mt-1 text-gray-900">{{ $item->condition }}</p>
                            </div>

                            <!-- Reported By -->
                            <div class="bg-gray-50 rounded-lg p-4">
                                <h3 class="text-sm font-medium text-gray-500">Reported By</h3>
                                <p class="mt-1 text-gray-900">
                                    @if ($item->is_anonymous)
                                        Anonymous
                                    @else
                                        {{ $item->user->name }}
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
