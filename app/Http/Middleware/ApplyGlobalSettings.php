<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\SettingsService;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class ApplyGlobalSettings
{
    protected $settingsService;

    public function __construct(SettingsService $settingsService)
    {
        $this->settingsService = $settingsService;
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $this->settingsService->applyGlobalSettings();
        } catch (\Exception $e) {
            // Log error but don't stop the request
            Log::error('Failed to apply global settings: ' . $e->getMessage());
        }

        return $next($request);
    }
} 