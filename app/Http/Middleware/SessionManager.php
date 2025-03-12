<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Contracts\Auth\Authenticatable;

class SessionManager
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if user is authenticated
        if (Auth::check()) {
            $user = Auth::user();

            // Update last activity timestamp
            Session::put('last_activity', Carbon::now());

            // Store user's timezone if available
            if ($request->header('X-Timezone')) {
                Session::put('user_timezone', $request->header('X-Timezone'));
            }

            // Check for concurrent sessions
            if ($this->hasOtherActiveSessions()) {
                // Optional: Force logout from other sessions
                // Auth::logoutOtherDevices(request('password'));

                // Or just notify the user
                Session::flash('warning', 'You have other active sessions on different devices.');
            }

            // Regenerate session ID periodically for security
            if (!Session::has('last_session_refresh') ||
                Carbon::parse(Session::get('last_session_refresh'))->addMinutes(30)->isPast()) {
                Session::regenerate();
                Session::put('last_session_refresh', Carbon::now());
            }

            // Store user preferences in session
            $this->storeUserPreferences($user);
        }

        return $next($request);
    }

    /**
     * Check for other active sessions
     *
     * @return bool
     */
    protected function hasOtherActiveSessions()
    {
        $sessions = DB::table('sessions')
            ->where('user_id', Auth::id())
            ->where('id', '!=', Session::getId())
            ->where('last_activity', '>=', Carbon::now()->subMinutes(config('session.lifetime')))
            ->count();

        return $sessions > 0;
    }

    /**
     * Store user preferences in session
     *
     * @param Authenticatable $user
     * @return void
     */
    protected function storeUserPreferences(Authenticatable $user)
    {
        // Store user settings in session for quick access
        if (!Session::has('user_preferences') || Session::get('preferences_updated_at') != $user->updated_at) {
            Session::put('user_preferences', [
                'locale' => $user->locale ?? config('app.locale'),
                'theme' => $user->theme ?? 'light',
                'notifications_enabled' => $user->notifications_enabled ?? true,
                // Add more user preferences as needed
            ]);
            Session::put('preferences_updated_at', $user->updated_at);
        }
    }
}
