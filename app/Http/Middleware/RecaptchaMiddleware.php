<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class RecaptchaMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!$request->has('g-recaptcha-response')) {
            return response()->json(['error' => 'Please complete the reCAPTCHA'], 422);
        }

        $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => config('services.recaptcha.secret_key'),
            'response' => $request->input('g-recaptcha-response'),
            'remoteip' => $request->ip()
        ]);

        if (!$response->json('success')) {
            return response()->json(['error' => 'Invalid reCAPTCHA'], 422);
        }

        return $next($request);
    }
}
