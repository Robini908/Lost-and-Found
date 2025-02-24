<!-- filepath: /c:/my-projects/lost-found/resources/views/components/jet-input.blade.php -->
@props(['disabled' => false])

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'form-input rounded-md shadow-sm']) !!}>
