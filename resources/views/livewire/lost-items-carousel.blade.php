<!-- filepath: /c:/my-projects/lost-found/resources/views/livewire/lost-items-carousel.blade.php -->
<div class="relative">
    <h2 class="text-2xl font-bold text-center mb-6">Recently Reported Items as Lost</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        @foreach ($lostItems as $item)
            <div class="bg-transparent shadow-lg rounded-lg overflow-hidden transition-transform transform hover:scale-105 hover:shadow-xl">
                <!-- Image Section -->
                <div class="relative h-48 w-full overflow-hidden">
                    @if ($item->images->isNotEmpty())
                        <img src="{{ asset('storage/' . $item->images->first()->image_path) }}"
                            alt="{{ $item->title }}"
                            class="absolute inset-0 w-full h-full object-cover aspect-square">
                    @else
                        <div class="absolute inset-0 w-full h-full bg-gray-200 flex items-center justify-center">
                            <span class="text-gray-500">No Image</span>
                        </div>
                    @endif
                </div>

                <!-- Content Section -->
                <div class="p-4">
                    <!-- Title with Eye Icon -->
                    <div class="flex justify-between items-center">
                        <h3 class="text-xl font-bold text-gray-800 mb-2">{{ $item->title }}</h3>
                    </div>

                    <!-- Reported By -->
                    <div class="flex items-center space-x-2 text-sm text-gray-600 mb-4">
                        <span class="font-medium">Reported by:</span>
                        <span class="text-gray-700">{{ $item->user->name }}</span>
                    </div>

                    <!-- Optional: Location and Value -->
                    <div class="flex items-center justify-between text-sm text-gray-600">
                        <span class="flex items-center space-x-1">
                            <!-- Pulsating Location Icon -->
                            <svg class="w-8 h-8 text-blue-500 animate-pulse" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17.657 16.657L13.414 20.9a2 2 0 01-2.828 0l-4.244-4.243a8 8 0 1111.314 0z">
                                </path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <span>{{ $item->location }}</span>
                        </span>
                    </div>
                    <div class="mt-4 flex justify-center">
                        <x-button onclick="window.location.href='{{ route('register', ['item_id' => \Illuminate\Support\Facades\Hash::make($item->id)]) }}'"

                            title="Claim Item">
                            Claim
                        </x-button>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
