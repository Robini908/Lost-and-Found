<x-layouts.app>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <h2 class="text-2xl font-bold text-gray-900">
                    <span class="bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">
                        {{ $item->item_type === 'found' ? 'Found Item' : 'Lost Item' }}
                    </span>
                </h2>
                <div class="flex items-center space-x-3">
                    <span class="px-3 py-1 text-sm {{ $item->is_verified ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }} rounded-full font-medium"
                          data-tippy-content="{{ $item->is_verified ? 'This item has been verified by our team' : 'This item is pending verification' }}">
                        <i class="fas {{ $item->is_verified ? 'fa-check' : 'fa-clock' }} mr-1"></i>
                        {{ $item->is_verified ? 'Verified' : 'Pending Verification' }}
                    </span>
                </div>
        </div>
            <div class="flex items-center space-x-4">
                @auth
                    @if($item->user_id !== auth()->id())
                        <livewire:contact-reporter :item="$item">
                            <x-slot name="trigger">
                                <button class="inline-flex items-center px-6 py-2.5 bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white text-sm font-medium rounded-full transition-all duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 shadow-md hover:shadow-lg transform hover:-translate-y-0.5"
                                        data-tippy-content="Contact the {{ $item->item_type === 'found' ? 'finder' : 'owner' }}">
                                    <i class="fas fa-envelope mr-2.5"></i>
                                    Contact
                                </button>
                            </x-slot>
                        </livewire:contact-reporter>
                    @endif

                    <livewire:share-item :itemId="$item->id">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center px-6 py-2.5 bg-white hover:bg-gray-50 active:bg-gray-100 text-gray-700 text-sm font-medium rounded-full transition-all duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-gray-200 focus:ring-offset-2 shadow-md hover:shadow-lg transform hover:-translate-y-0.5 border border-gray-200"
                                    data-tippy-content="Share this item">
                                <i class="fas fa-share-alt mr-2.5 text-gray-600"></i>
                                Share
                            </button>
                        </x-slot>
                    </livewire:share-item>

                    <div class="relative" x-data="{ isOpen: false }">
                        <livewire:report-item :item="$item">
                            <x-slot name="trigger">
                                <button class="inline-flex items-center px-6 py-2.5 bg-white hover:bg-gray-50 active:bg-gray-100 text-gray-700 text-sm font-medium rounded-full transition-all duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-gray-200 focus:ring-offset-2 shadow-md hover:shadow-lg transform hover:-translate-y-0.5 border border-gray-200"
                                        data-tippy-content="Report this item">
                                    <i class="fas fa-flag mr-2.5 text-gray-600"></i>
                                    Report
                                </button>
                            </x-slot>
                        </livewire:report-item>
                    </div>
                @endauth
            </div>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Main Content -->
                <div class="lg:col-span-2">
                    <!-- Image Gallery Card -->
                    <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow duration-300 overflow-hidden mb-6">
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                                    <i class="fas fa-images text-blue-600 mr-2"></i>
                                    Item Images
                                </h3>
                                @if($item->images->count() > 1)
                                    <span class="text-sm text-gray-500 bg-gray-100 px-3 py-1 rounded-full">
                                        <i class="fas fa-photo-film mr-1"></i>
                                        {{ $item->images->count() }} photos
                                    </span>
                                @endif
                            </div>
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                                @foreach($item->images as $image)
                                    <div class="relative aspect-w-1 aspect-h-1 rounded-xl overflow-hidden group shadow-sm hover:shadow-md transition-shadow duration-300">
                                        <img src="{{ Storage::url($image->image_path) }}"
                                             alt="Item image"
                                             class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-300">
                                        <div class="absolute inset-0 bg-gradient-to-t from-black/30 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Item Details Card -->
                    <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow duration-300 overflow-hidden">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                <i class="fas fa-circle-info text-blue-600 mr-2"></i>
                                Item Details
                            </h3>
                            <div class="space-y-6">
                                <!-- Title and Description -->
                                <div>
                                    <h1 class="text-2xl font-bold text-gray-900 mb-3">{{ $item->title }}</h1>
                                    <p class="text-gray-600 leading-relaxed">{{ $item->description }}</p>
                                </div>

                                <!-- Key Details Grid -->
                                <div class="grid grid-cols-2 md:grid-cols-3 gap-6 mt-6">
                                    <!-- Category -->
                                    <div class="bg-gray-50 p-4 rounded-lg hover:bg-gray-100 transition-colors duration-200" data-tippy-content="Item Category">
                                        <span class="text-sm text-gray-500 block mb-1 flex items-center">
                                            <i class="fas fa-folder text-blue-600 mr-2"></i>
                                            Category
                                        </span>
                                        <span class="font-medium text-gray-900">{{ $item->category->name }}</span>
                                    </div>

                                    <!-- Date -->
                                    <div class="bg-gray-50 p-4 rounded-lg hover:bg-gray-100 transition-colors duration-200" data-tippy-content="Date {{ $item->item_type === 'found' ? 'Found' : 'Lost' }}">
                                        <span class="text-sm text-gray-500 block mb-1 flex items-center">
                                            <i class="fas fa-calendar text-blue-600 mr-2"></i>
                                            Date
                                        </span>
                                        <span class="font-medium text-gray-900">{{ $item->date_lost?->format('M d, Y') }}</span>
                                    </div>

                                    <!-- Location -->
                                    <div class="bg-gray-50 p-4 rounded-lg hover:bg-gray-100 transition-colors duration-200" data-tippy-content="Location Details">
                                        <span class="text-sm text-gray-500 block mb-1 flex items-center">
                                            <i class="fas fa-location-dot text-blue-600 mr-2"></i>
                                            Location
                                        </span>
                                        <span class="font-medium text-gray-900">
                                            {{ $item->location_type === 'map' ? $item->location_address : $item->area }}
                                        </span>
                                    </div>

                                    @if($item->brand)
                                        <div class="bg-gray-50 p-4 rounded-lg hover:bg-gray-100 transition-colors duration-200" data-tippy-content="Item Brand">
                                            <span class="text-sm text-gray-500 block mb-1 flex items-center">
                                                <i class="fas fa-tag text-blue-600 mr-2"></i>
                                                Brand
                                            </span>
                                            <span class="font-medium text-gray-900">{{ $item->brand }}</span>
                                        </div>
                                    @endif

                                    @if($item->model)
                                        <div class="bg-gray-50 p-4 rounded-lg hover:bg-gray-100 transition-colors duration-200" data-tippy-content="Item Model">
                                            <span class="text-sm text-gray-500 block mb-1 flex items-center">
                                                <i class="fas fa-cube text-blue-600 mr-2"></i>
                                                Model
                                            </span>
                                            <span class="font-medium text-gray-900">{{ $item->model }}</span>
                                        </div>
                                    @endif

                                    @if($item->color)
                                        <div class="bg-gray-50 p-4 rounded-lg hover:bg-gray-100 transition-colors duration-200" data-tippy-content="Item Color">
                                            <span class="text-sm text-gray-500 block mb-1 flex items-center">
                                                <i class="fas fa-palette text-blue-600 mr-2"></i>
                                                Color
                                            </span>
                                            <span class="font-medium text-gray-900">{{ $item->color }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="lg:col-span-1">
                    <!-- Reporter Card -->
                    <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow duration-300 overflow-hidden mb-6">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                <i class="fas fa-user text-blue-600 mr-2"></i>
                                {{ $item->item_type === 'found' ? 'Found By' : 'Reported By' }}
                            </h3>
                            <div class="flex items-center p-4 bg-gray-50 rounded-lg">
                                <div class="flex-shrink-0">
                                    <img class="h-14 w-14 rounded-full ring-2 ring-white shadow-sm" src="{{ $item->user->profile_photo_url }}" alt="{{ $item->user->name }}">
                                </div>
                                <div class="ml-4">
                                    <h4 class="text-sm font-medium text-gray-900">{{ $item->user->name }}</h4>
                                    <p class="text-sm text-gray-500 mt-1">Member since {{ $item->user->created_at->format('M Y') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Status Timeline -->
                    <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow duration-300 overflow-hidden">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                <i class="fas fa-clock-rotate-left text-blue-600 mr-2"></i>
                                Item Timeline
                            </h3>
                            <div class="space-y-6 relative">
                                <div class="relative pl-8 pb-6">
                                    <div class="absolute left-[-5px] top-0 w-4 h-4 rounded-full bg-blue-600 ring-4 ring-blue-100"></div>
                                    <div class="ml-6">
                                        <p class="text-sm text-gray-500">Item {{ $item->item_type === 'found' ? 'found' : 'reported lost' }}</p>
                                        <p class="text-sm font-medium text-gray-900 mt-1">{{ $item->created_at->format('M d, Y h:i A') }}</p>
                                    </div>
                                </div>
                                @if($item->is_verified)
                                    <div class="relative pl-8 pb-6">
                                        <div class="absolute left-[-5px] top-0 w-4 h-4 rounded-full bg-green-600 ring-4 ring-green-100"></div>
                                        <div class="ml-6">
                                            <p class="text-sm text-gray-500">Item verified</p>
                                            <p class="text-sm font-medium text-gray-900 mt-1">{{ $item->verified_at?->format('M d, Y h:i A') }}</p>
                                        </div>
                                    </div>
                                @endif
                                <div class="absolute left-[-1px] top-0 bottom-0 w-0.5 bg-gray-200"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Material Design-like ripple effect
            const buttons = document.querySelectorAll('button');
            buttons.forEach(button => {
                button.addEventListener('click', function(e) {
                    const x = e.clientX - e.target.offsetLeft;
                    const y = e.clientY - e.target.offsetTop;

                    const ripple = document.createElement('span');
                    ripple.style.left = `${x}px`;
                    ripple.style.top = `${y}px`;
                    ripple.classList.add('ripple');

                    this.appendChild(ripple);

                    setTimeout(() => {
                        ripple.remove();
                    }, 600);
                });
            });
        });
    </script>

    <style>
        .ripple {
            position: absolute;
            background: rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            transform: scale(0);
            animation: ripple-animation 0.6s linear;
            pointer-events: none;
        }

        @keyframes ripple-animation {
            to {
                transform: scale(4);
                opacity: 0;
            }
        }

        button {
            position: relative;
            overflow: hidden;
        }
    </style>
    @endpush
</x-layouts.app>
