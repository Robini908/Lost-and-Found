@props(['trigger', 'items', 'tooltip' => null])

<div x-data="{ open: false }" class="relative">
    <div @click="open = !open" class="cursor-pointer" {{ $tooltip ? 'data-tippy-content="' . htmlspecialchars($tooltip) . '"' : '' }}>
        {{ $trigger }}
    </div>

    <div x-show="open" @click.away="open = false"
        class="origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-gray-100 ring-1 ring-black ring-opacity-5 focus:outline-none"
        role="menu" aria-orientation="vertical" aria-labelledby="menu-button" tabindex="-1">
        <div class="py-1" role="none">
            {{ $items }}
        </div>
    </div>
</div>
`
