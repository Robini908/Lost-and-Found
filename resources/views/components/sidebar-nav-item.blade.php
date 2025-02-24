<!-- resources/views/components/sidebar-nav-item.blade.php -->
@props(['route', 'icon', 'label', 'sublinks' => []])

<div x-data="{ open: false }" class="space-y-1">
    <a href="{{ $route }}" @click="open = !open"
        class="group flex items-center px-2 py-2 text-base font-medium text-gray-900 rounded-md hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition ease-in-out duration-500">
        <i class="{{ $icon }} mr-4 h-6 w-6 text-gray-400 group-hover:text-gray-500"></i>
        {{ $label }}
        @if (count($sublinks) > 0)
            <svg class="ml-auto h-5 w-5 text-gray-400 group-hover:text-gray-500 transition-transform transform duration-500"
                :class="{ 'rotate-180': open }" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        @endif
    </a>
    @if (count($sublinks) > 0)
        <div x-show="open" x-transition:enter="transition ease-out duration-500"
            x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100"
            x-transition:leave="transition ease-in duration-500" x-transition:leave-start="opacity-100 transform scale-100"
            x-transition:leave-end="opacity-0 transform scale-95" class="space-y-1 pl-8 bg-gray-100 rounded-md">
            @foreach ($sublinks as $sublink)
                <x-sidebar-nav-item :route="$sublink['route']" :icon="$sublink['icon']" :label="$sublink['label']"
                    :sublinks="$sublink['sublinks'] ?? []" class="bg-gray-200" />
            @endforeach
        </div>
    @endif
</div>
