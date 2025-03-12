<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Session;
use App\Services\TranslationService;

class LanguageSwitcher extends Component
{
    public $currentLocale;
    protected $translationService;
    protected $locales = [
        'en' => ['name' => 'English', 'flag' => 'gb'],
        'es' => ['name' => 'Español', 'flag' => 'es'],
        'fr' => ['name' => 'Français', 'flag' => 'fr'],
        'de' => ['name' => 'Deutsch', 'flag' => 'de'],
        'it' => ['name' => 'Italiano', 'flag' => 'it'],
    ];

    public function boot(TranslationService $translationService)
    {
        $this->translationService = $translationService;
    }

    public function mount()
    {
        $this->currentLocale = App::getLocale();
    }

    public function switchLanguage($locale)
    {
        if (!array_key_exists($locale, $this->locales)) {
            $this->dispatch('showToast', [
                'message' => $this->translationService->getTranslation('language_switch_error'),
                'type' => 'error'
            ]);
            return;
        }

        try {
            App::setLocale($locale);
            Session::put('locale', $locale);
            Cookie::queue('locale', $locale, 43200); // 30 days
            $this->currentLocale = $locale;

            $this->dispatch('showToast', [
                'message' => $this->translationService->getTranslation('language_switched', ['language' => $this->locales[$locale]['name']]),
                'type' => 'success'
            ]);

            $this->dispatch('language-changed', ['locale' => $locale]);
        } catch (\Exception $e) {
            $this->dispatch('showToast', [
                'message' => $this->translationService->getTranslation('language_switch_error'),
                'type' => 'error'
            ]);
        }
    }

    public function getAvailableLocales()
    {
        return $this->locales;
    }

    public function render()
    {
        return view('livewire.language-switcher', [
            'locales' => $this->locales
        ]);
    }
}
