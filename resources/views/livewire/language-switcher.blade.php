<div>
    <div class="flex space-x-4">
        @foreach (config('app.locales') as $locale => $language)
            <button wire:click="switchLanguage('{{ $locale }}')" class="{{ $locale == $currentLanguage ? 'bg-blue-500 text-white' : '' }}">
                {{ strtoupper($locale) }}
            </button>
        @endforeach
    </div>
</div>