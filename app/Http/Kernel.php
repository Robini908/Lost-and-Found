<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array<int, class-string|string>
     */
    protected $middleware = [
        \Illuminate\Foundation\Http\Middleware\InvokeDeferredCallbacks::class,
        \Illuminate\Http\Middleware\HandleCors::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
    ];

    /**
     * The application's middleware groups.
     *
     * @return array<string, array<int, class-string|string>>
     */
    protected function middlewareGroups(): array
    {
        return [
            'web' => [
                \Illuminate\Foundation\Http\Middleware\InvokeDeferredCallbacks::class,
                \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
                \Illuminate\Session\Middleware\StartSession::class,
                \Illuminate\View\Middleware\ShareErrorsFromSession::class,
                \App\Http\Middleware\VerifyCsrfToken::class, // Ensure this line exists
                \Illuminate\Routing\Middleware\SubstituteBindings::class,
                \App\Http\Middleware\XssProtection::class,
                \App\Http\Middleware\SqlInjectionProtection::class,
                \App\Http\Middleware\EnhancedCsrfProtection::class,
            ],

            'api' => [
                \App\Http\Middleware\ApiTokenMiddleware::class,
                \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
                'throttle:api',
                \Illuminate\Routing\Middleware\SubstituteBindings::class,
                \App\Http\Middleware\CustomRateLimiter::class,
                \App\Http\Middleware\DataEncryption::class,
            ],
        ];
    }

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @return array<string, class-string|string>
     */
    protected function routeMiddleware(): array
    {
        return [
            'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
            'auth.session' => \Illuminate\Session\Middleware\AuthenticateSession::class,
            'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
            'can' => \Illuminate\Auth\Middleware\Authorize::class,
            'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
            'signed' => \Illuminate\Routing\Middleware\ValidateSignature::class,
            'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
            'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,

            // Custom middleware
            'bypass.verified' => \App\Http\Middleware\BypassEmailVerification::class,
            'security.headers' => \App\Http\Middleware\SecurityHeaders::class,
            'rate.limit' => \App\Http\Middleware\CustomRateLimiter::class,
            'xss.protect' => \App\Http\Middleware\XssProtection::class,
            'sql.protect' => \App\Http\Middleware\SqlInjectionProtection::class,
            'encrypt.data' => \App\Http\Middleware\DataEncryption::class,
            'recaptcha' => \App\Http\Middleware\Recaptcha::class,
        ];
    }

    protected $middlewareAliases = [
        'auth' => \App\Http\Middleware\Authenticate::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
    ];
}
