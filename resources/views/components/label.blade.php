@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-medium text-sm text-gray-700']) }}>
    {{ $value ?? $slot }}
</label>

{{-- @props(['for', 'value'])

<label {{ $attributes->merge(['class' => 'block text-sm font-medium text-gray-700']) }} for="{{ $for }}">
    {{ $value ?? $slot }}
</label> --}}
