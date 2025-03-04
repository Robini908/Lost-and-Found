<?php

namespace App\Providers;

use App\Http\Middleware\Recaptcha;
use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Http\Kernel;

class RecaptchaServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(Recaptcha::class);
    }

    public function boot(): void
    {
        $router = $this->app['router'];
        $router->aliasMiddleware('recaptcha', Recaptcha::class);
    }
}
