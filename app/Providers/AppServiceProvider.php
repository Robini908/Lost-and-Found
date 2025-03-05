<?php

namespace App\Providers;

use App\Models\LostItemImage;
use App\Services\RoleService;
use Illuminate\Support\ServiceProvider;
use App\Observers\LostItemImageObserver;
use OpenAI\Client;
use OpenAI;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use App\Services\RolePermissionService;
use Illuminate\Support\Facades\Blade;
use App\Facades\RolePermission;
use Livewire\Livewire;
use App\Livewire\ManageUsers;
use App\Livewire\Settings;
use App\Livewire\VerifyClaim;
use App\Livewire\Analytics;
use App\Livewire\ManagementSettings;
use App\Http\Middleware\SecurityHeaders;
use App\Http\Middleware\XssProtection;
use App\Http\Middleware\SqlInjectionProtection;
use App\Http\Middleware\CustomRateLimiter;
use App\Http\Middleware\DataEncryption;
use App\Livewire\MyReportedItems;
use App\Services\HashIdService;
use App\Livewire\ImpersonateUser;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(RoleService::class, function () {
            return new RoleService();
        });
        $this->app->singleton(Client::class, function () {
            return OpenAI::client(config('services.openai.api_key'));
        });
        $this->app->singleton('role-permission', function ($app) {
            return new RolePermissionService();
        });

        // Register Security Middleware
        $this->app->singleton('security.headers', function ($app) {
            return new SecurityHeaders();
        });

        $this->app->singleton('xss.protect', function ($app) {
            return new XssProtection();
        });

        $this->app->singleton('sql.protect', function ($app) {
            return new SqlInjectionProtection();
        });

        $this->app->singleton('rate.limit', function ($app) {
            return new CustomRateLimiter();
        });

        $this->app->singleton('encrypt.data', function ($app) {
            return new DataEncryption();
        });

        $this->app->singleton(HashIdService::class, function ($app) {
            return new HashIdService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        LostItemImage::observe(LostItemImageObserver::class);
        if (Session::has('locale')) {
            App::setLocale(Session::get('locale'));
        }

        Blade::if('role', function ($role) {
            if (!auth()->check()) return false;

            return RolePermission::hasHigherOrEqualPriority(
                RolePermission::getHighestRole(Auth()->user()),
                $role
            );
        });

        Blade::if('can', function ($permission) {
            if (!Auth()->check()) return false;

            return RolePermission::hasAnyPermission(
                auth()->user(),
                (array) $permission
            );
        });


    }
}
