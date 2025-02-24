<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        // Add routes to exclude from CSRF protection here
        '/send-sms',
        '/send-whatsapp',
        '/twilio/webhook', // Add this if you're handling incoming webhooks
    ];
}
