@props(['id' => null, 'disabled' => false])

<div 
    x-data="{ checked: @entangle($attributes->wire('model')) }" 
    class="relative inline-block"
    x-id="['toggle-button']"
>
    <input 
        type="checkbox"
        :id="$id('toggle-button')"
        {{ $disabled ? 'disabled' : '' }}
        {{ $attributes->merge(['class' => 'sr-only peer']) }}
        role="switch"
        x-model="checked"
    >
    <label
        :for="$id('toggle-button')"
        class="relative inline-flex h-8 w-14 items-center rounded-full transition-colors duration-300 ease-in-out
            {{ $disabled 
                ? 'bg-gray-200 cursor-not-allowed' 
                : 'cursor-pointer bg-gray-200 peer-checked:bg-primary-600 hover:bg-gray-300 peer-checked:hover:bg-primary-700'
            }}"
    >
        <span 
            class="inline-block h-6 w-6 transform rounded-full bg-white shadow-md ring-0 transition duration-300 ease-in-out peer-checked:translate-x-7"
            :class="{ 'translate-x-1': !checked, 'translate-x-7': checked }"
        ></span>
        <span class="sr-only">Toggle</span>
    </label>
</div>

@once
@push('styles')
<style>
    /* Material Design elevation and ripple effect */
    .peer:checked + label span {
        box-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1);
    }
    
    /* Material Design focus ring */
    .peer:focus + label {
        @apply ring-2 ring-primary-500 ring-offset-2;
    }
    
    /* Material Design disabled state */
    .peer:disabled + label {
        @apply opacity-50;
    }
    
    /* Material Design color variables */
    :root {
        --md-sys-color-primary: rgb(var(--color-primary-600));
        --md-sys-color-on-primary: rgb(var(--color-white));
    }
</style>
@endpush
@endonce 