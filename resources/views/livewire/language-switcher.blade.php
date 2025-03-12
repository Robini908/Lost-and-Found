<div class="relative" x-data="{ open: false }">
    <button @click="open = !open"
            class="text-gray-500 hover:text-gray-700 transition duration-150 ease-in-out flex items-center gap-1"
            aria-label="{{ __('messages.switch_language') }}">
        <span class="fi fi-{{ $locales[$currentLocale]['flag'] }} rounded-sm"></span>
        <span class="text-sm font-medium hidden sm:block">{{ $locales[$currentLocale]['name'] }}</span>
        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
        </svg>
    </button>

    <div x-show="open"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="transform opacity-0 scale-95"
         x-transition:enter-end="transform opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="transform opacity-100 scale-100"
         x-transition:leave-end="transform opacity-0 scale-95"
         @click.away="open = false"
         class="absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-50"
         role="menu"
         aria-orientation="vertical"
         aria-labelledby="language-menu">

        @foreach($locales as $code => $lang)
            <button wire:click="switchLanguage('{{ $code }}')"
                    @click="open = false"
                    class="w-full flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ $currentLocale === $code ? 'bg-gray-50' : '' }}"
                    role="menuitem">
                <span class="fi fi-{{ $lang['flag'] }} rounded-sm mr-2"></span>
                <span>{{ $lang['name'] }}</span>
                @if($currentLocale === $code)
                    <svg class="ml-auto h-4 w-4 text-green-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                    </svg>
                @endif
            </button>
        @endforeach
    </div>
</div>
