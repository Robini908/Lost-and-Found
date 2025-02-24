<!-- filepath: /c:/my-projects/lost-found/resources/views/components/jet-label.blade.php -->
@props(['for'])

<label {{ $attributes->merge(['class' => 'block font-medium text-sm text-gray-700']) }} for="{{ $for }}">
    {{ $slot }}
</label>
