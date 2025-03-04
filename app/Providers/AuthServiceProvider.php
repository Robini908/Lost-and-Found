<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\ItemClaim;
use App\Policies\ItemClaimPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->bind(ItemClaim::class, ItemClaimPolicy::class);
    }
}
