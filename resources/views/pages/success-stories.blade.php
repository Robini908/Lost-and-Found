<x-guest-layout>
    <x-slot name="title">Success Stories</x-slot>

    <div class="min-h-screen bg-gradient-to-b from-blue-50 to-white">
        <!-- Hero Section -->
        <div class="pt-32 pb-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <h1 class="text-4xl md:text-5xl font-bold text-gray-900 text-center mb-6">
                    Success Stories
                </h1>
                <p class="text-xl text-gray-600 text-center max-w-3xl mx-auto">
                    Real stories from people who have successfully recovered their lost items through our platform.
                </p>
            </div>
        </div>

        <!-- Success Stories Grid -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-24">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($successStories as $story)
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden transition-all duration-300 hover:shadow-2xl">
                        @if($story->images->first())
                            <img src="{{ Storage::url($story->images->first()->path) }}"
                                 alt="Item Image"
                                 class="w-full h-48 object-cover">
                        @endif
                        <div class="p-6">
                            <div class="flex items-center mb-4">
                                <span class="px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                    {{ $story->category->name }}
                                </span>
                                <span class="ml-2 text-sm text-gray-500">
                                    {{ $story->created_at->diffForHumans() }}
                                </span>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-900 mb-2">
                                {{ $story->title }}
                            </h3>
                            <p class="text-gray-600 mb-4">
                                {{ Str::limit($story->description, 150) }}
                            </p>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <i class="fas fa-clock text-blue-500 mr-2"></i>
                                    <span class="text-sm text-gray-500">
                                        Recovered in {{ $story->created_at->diffInHours($story->updated_at) }} hours
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-guest-layout>
