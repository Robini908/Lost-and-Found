<?php

namespace App\Providers;

use App\Models\LostItemImage;
use App\Services\RoleService;
use Illuminate\Support\ServiceProvider;
use App\Observers\LostItemImageObserver;
use OpenAI\Client;
use OpenAI;



class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register()
    {
        $this->app->singleton(RoleService::class, function () {
            return new RoleService();
        });
        $this->app->singleton(Client::class, function () {
            return OpenAI::client(config('services.openai.api_key'));
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        LostItemImage::observe(LostItemImageObserver::class);
    }
}
