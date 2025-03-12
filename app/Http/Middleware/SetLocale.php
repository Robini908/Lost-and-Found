<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use App\Services\TranslationService;

class SetLocale
{
    protected $translationService;

    public function __construct(TranslationService $translationService)
    {
        $this->translationService = $translationService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $locale = Session::get('locale');

        // If no locale in session, try from cookie
        if (!$locale) {
            $locale = $request->cookie('locale');
        }

        // If still no locale, try from browser
        if (!$locale) {
            $locale = $request->getPreferredLanguage($this->translationService->getSupportedLocales());
        }

        // If still no locale or not supported, use default
        if (!$locale || !in_array($locale, $this->translationService->getSupportedLocales())) {
            $locale = config('app.fallback_locale', 'en');
        }

        // Set the application locale
        App::setLocale($locale);
        Session::put('locale', $locale);

        return $next($request);
    }
}
