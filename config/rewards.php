<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Reward Points Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains the configuration for the reward points system.
    |
    */

    // Points awarded for reporting a found item
    'points_per_found_item' => env('POINTS_PER_FOUND_ITEM', 100),

    // Points expiration period in days
    'points_expiration_days' => env('POINTS_EXPIRATION_DAYS', 365),

    // Minimum points required for conversion
    'min_points_for_conversion' => env('MIN_POINTS_FOR_CONVERSION', 100),

    // Points to currency conversion rate
    'points_to_currency_rate' => env('POINTS_TO_CURRENCY_RATE', 0.01),
];
