<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Authentication Guard
    |--------------------------------------------------------------------------
    |
    | Please specify your default authentication guard to be used by impersonate.
    |
    */
    'guard' => 'sanctum',

    /*
    |--------------------------------------------------------------------------
    | Session Key
    |--------------------------------------------------------------------------
    |
    | This is the session key used by impersonate.
    |
    */
    'session_key' => 'impersonate',

    /*
    |--------------------------------------------------------------------------
    | Session Guard Key
    |--------------------------------------------------------------------------
    |
    | This is the session key used by impersonate for the guard.
    |
    */
    'session_guard_key' => 'impersonate_guard',

    /*
    |--------------------------------------------------------------------------
    | Session Take Key
    |--------------------------------------------------------------------------
    |
    | This is the session key used by impersonate when taking impersonation.
    |
    */
    'session_take_key' => 'impersonate_take',

    /*
    |--------------------------------------------------------------------------
    | Impersonate Redirect URLs
    |--------------------------------------------------------------------------
    |
    | Please define where to redirect after impersonating/leaving.
    | `take_redirect_to` is used when taking an impersonation.
    | `leave_redirect_to` is used when leaving an impersonation.
    |
    | These values can be:
    | - A valid URL
    | - A named route
    | - A Laravel response (redirect('/'), redirect()->route('home'))
    | - A closure (returns a Response instance)
    | - null (redirects back to previous URL)
    |
    */
    'take_redirect_to' => '/dashboard',
    'leave_redirect_to' => '/dashboard',

    /*
    |--------------------------------------------------------------------------
    | Impersonate Blade Directive
    |--------------------------------------------------------------------------
    |
    | You can customize the blade directive name.
    |
    */
    'blade_directive' => 'impersonate',
];
