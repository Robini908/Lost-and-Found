@props(['disabled' => false])

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'border-gray-300 focus:border-green-700 focus:ring-green-100 rounded-md shadow-sm']) !!}>
