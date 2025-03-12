<?php

namespace App\Providers;

use App\Services\TranslationService;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Lang;
use Illuminate\Translation\Translator;

class TranslationServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(TranslationService::class, function ($app) {
            return new TranslationService();
        });
    }

    public function boot()
    {
        $this->app->extend('translator', function ($translator, $app) {
            $loader = $app['translation.loader'];
            $locale = $app['config']['app.locale'];

            $translator = new Translator($loader, $locale);

            return $translator;
        });

        // Extend Laravel's translation functionality
        Lang::macro('getFromDatabase', function ($key, $replace = [], $locale = null) {
            return app(TranslationService::class)->getTranslation($key, $replace, $locale);
        });
    }
}
