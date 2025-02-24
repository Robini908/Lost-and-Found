
<!-- filepath: /c:/my-projects/lost-found/resources/views/components/jet-input-error.blade.php -->
@props(['for'])

@error($for)
    <p {{ $attributes->merge(['class' => 'text-sm text-red-600']) }}>{{ $message }}</p>
@enderror
