<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Image Processing Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains the configuration for image processing in the application.
    | These settings are optimized for maximum quality and detail preservation.
    |
    */

    'quality' => [
        // Maximum quality setting for JPEG images (0-100)
        'jpeg' => 100,

        // PNG compression level (0-9)
        'png' => 0, // No compression for maximum quality
    ],

    'dimensions' => [
        // Target dimensions for upscaling (8K)
        'max_width' => 7680,    // 8K width
        'max_height' => 4320,   // 8K height

        // Minimum dimensions (4K)
        'min_width' => 3840,    // 4K width
        'min_height' => 2160,   // 4K height
    ],

    'optimization' => [
        // Enable or disable image optimization
        'enabled' => true,

        // Enhanced sharpening amount (0-100)
        'sharpen' => 25,

        // Brightness adjustment (-100 to 100)
        'brightness' => 2,

        // Contrast adjustment (-100 to 100)
        'contrast' => 15,

        // Color enhancement
        'saturation' => 5,      // Slight increase in color saturation
        'gamma' => 1.1,         // Gamma correction for better detail
    ],

    'processing' => [
        // Progressive upscaling settings
        'upscale_factor' => 1.5,    // Maximum scale factor per step
        'quality_steps' => true,     // Enable progressive quality enhancement

        // Edge enhancement
        'edge_enhancement' => true,
        'edge_radius' => 0.5,
        'edge_amount' => 20,

        // Noise reduction
        'noise_reduction' => true,
        'noise_level' => 0.5,
    ],

    'storage' => [
        // Maximum file size in megabytes (increased for higher quality)
        'max_size' => 100,

        // Allowed mime types
        'allowed_types' => [
            'image/jpeg',
            'image/png',
            'image/webp',
        ],

        // Preserve original
        'keep_original' => true,
    ],

    'advanced' => [
        // Enable advanced processing features
        'progressive_jpeg' => true,  // Use progressive JPEG encoding
        'strip_metadata' => false,   // Preserve image metadata
        'auto_orient' => true,       // Automatically orient image based on EXIF
        'preserve_color_profile' => true, // Preserve color profiles
    ],

    /*
    |--------------------------------------------------------------------------
    | Image Driver
    |--------------------------------------------------------------------------
    |
    | Intervention Image supports "GD Library" and "Imagick" to process images
    | internally. You may choose one of them according to your PHP
    | configuration. By default PHP's "GD Library" implementation is used.
    |
    | Supported: "gd", "imagick"
    |
    */

    'driver' => 'gd'
];
