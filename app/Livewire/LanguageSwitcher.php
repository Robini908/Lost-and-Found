<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\App;

class LanguageSwitcher extends Component
{
    public $currentLanguage;

    public function mount()
    {
        $this->currentLanguage = App::getLocale();
    }

    public function switchLanguage($lang)
    {
        session()->put('locale', $lang);
        App::setLocale($lang);
        $this->currentLanguage = $lang;
    }

    public function render()
    {
        return view('livewire.language-switcher');
    }
}