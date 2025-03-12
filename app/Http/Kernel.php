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
        // \App\Http\Middleware\TrustHosts::class,
        \App\Http\Middleware\TrustProxies::class,
        \Illuminate\Http\Middleware\HandleCors::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
    ];

    /**
     * The application's middleware groups.
     *
     * @var array<string, array<int, class-string|string>>
     */
    protected $middlewareGroups = [
        'web' => [
            \Illuminate\Cookie\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            \App\Http\Middleware\XssProtection::class,
            \App\Http\Middleware\SqlInjectionProtection::class,
            \App\Http\Middleware\EnhancedCsrfProtection::class,
            \App\Http\Middleware\ImpersonateMiddleware::class,
            \App\Http\Middleware\SetLocale::class,
            \App\Http\Middleware\ApplyGlobalSettings::class,
            \App\Http\Middleware\SessionManager::class,
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

    /**
     * The application's middleware aliases.
     *
     * Aliases may be used instead of class names to conveniently assign middleware to routes and groups.
     *
     * @var array<string, class-string|string>
     */
    protected $middlewareAliases = [
        'auth' => \App\Http\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'auth.session' => \Illuminate\Session\Middleware\AuthenticateSession::class,
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
        'signed' => \Illuminate\Routing\Middleware\ValidateSignature::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
        'bypass.verified' => \App\Http\Middleware\BypassEmailVerification::class,
        'security.headers' => \App\Http\Middleware\SecurityHeaders::class,
        'rate.limit' => \App\Http\Middleware\CustomRateLimiter::class,
        'xss.protect' => \App\Http\Middleware\XssProtection::class,
        'sql.protect' => \App\Http\Middleware\SqlInjectionProtection::class,
        'encrypt.data' => \App\Http\Middleware\DataEncryption::class,
        'recaptcha' => \App\Http\Middleware\Recaptcha::class,
    ];
}
