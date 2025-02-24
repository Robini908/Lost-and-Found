@props(['name', 'value', 'label', 'checked' => false])

<div class="flex items-center">
    <input
        type="radio"
        name="{{ $name }}"
        value="{{ $value }}"
        {{ $checked ? 'checked' : '' }}
        {{ $attributes->merge(['class' => 'h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500']) }}
    >
    <x-label for="{{ $name }}" :value="$label" />
</div>
