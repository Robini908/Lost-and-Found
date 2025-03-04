<?php

namespace App\Providers;

use Illuminate\Support\Facades\DB;
use App\Http\Middleware\XssProtection;
use Illuminate\Support\Facades\Schema;
use App\Http\Middleware\DataEncryption;
use Illuminate\Support\ServiceProvider;
use App\Http\Middleware\SecurityHeaders;
use Illuminate\Support\Facades\Validator;
use App\Http\Middleware\CustomRateLimiter;
use App\Http\Middleware\SqlInjectionProtection;

class SecurityServiceProvider extends ServiceProvider
{
    public function register()
    {
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
    }

    public function boot()
    {
        // Add custom security validation rules
        Validator::extend('secure_hash', function ($attribute, $value, $parameters) {
            return preg_match('/^[a-zA-Z0-9]{16,}$/', $value);
        });

        // Set secure defaults
        Schema::defaultStringLength(191);

        // Enable strict mode for database
        DB::statement('SET SESSION sql_mode = "STRICT_ALL_TABLES"');
    }
}
