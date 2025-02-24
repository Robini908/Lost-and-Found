<!-- filepath: /c:/my-projects/lost-found/resources/views/components/card-info.blade.php -->
@props(['title', 'description'])

<div class="bg-white shadow-md rounded-lg p-4 sm:p-6 lg:p-8">
    @if($title)
        <h3 class="text-lg sm:text-xl font-bold mb-2">{{ $title }}</h3>
    @endif
    @if($description)
        <p class="text-gray-600 mb-4">{{ $description }}</p>
    @endif
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 sm:gap-6">
        {{ $slot }}
    </div>
</div>
