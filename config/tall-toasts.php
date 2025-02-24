<?php

return [
    /*
     * General Settings
     */

    // How long each toast will be displayed before fading out, in milliseconds (ms)
    'duration' => 5000,

    // How long to wait before displaying the toasts after page loads, in milliseconds (ms)
    'load_delay' => 400,

    /*
     * Toast Positioning
     * Define the position where the toasts will appear on the screen.
     * Available options: 'top-right', 'top-left', 'bottom-right', 'bottom-left', 'top-center', 'bottom-center'
     */
    'position' => 'top-right',

    /*
     * Toast Styling
     * Customize the appearance of the toasts.
     */
    'style' => [
        // Background color for the toast (e.g., 'bg-blue-500', 'bg-green-500', 'bg-red-500')
        'background' => 'bg-blue-500',

        // Text color for the toast (e.g., 'text-white', 'text-black')
        'text_color' => 'text-white',

        // Padding for the toast (e.g., 'p-4', 'p-2')
        'padding' => 'p-4',

        // Border radius for the toast (e.g., 'rounded-lg', 'rounded-full')
        'border_radius' => 'rounded-lg',

        // Box shadow for the toast (e.g., 'shadow-lg', 'shadow-none')
        'box_shadow' => 'shadow-lg',
    ],

    /*
     * Animation Settings
     * Customize the animation for toast entry and exit.
     */
    'animation' => [
        // Animation for toast entry (e.g., 'fadeIn', 'slideInRight', 'slideInLeft')
        'enter' => 'fadeIn',

        // Animation for toast exit (e.g., 'fadeOut', 'slideOutRight', 'slideOutLeft')
        'exit' => 'fadeOut',

        // Animation duration in milliseconds (ms)
        'duration' => 500,
    ],

    /*
     * Session Keys
     * Define the session keys used for storing toast messages.
     * No need to edit unless the keys conflict with existing ones.
     */
    'session_keys' => [
        // Key for storing toasts to be displayed on the current page
        'toasts' => 'toasts',

        // Key for storing toasts to be displayed on the next page load
        'toasts_next_page' => 'toasts-next',
    ],

    /*
     * Toast Types
     * Define different types of toasts and their configurations.
     */
    'types' => [
        'success' => [
            'background' => 'bg-green-500',
            'icon' => 'check-circle', // Icon for success toasts (e.g., FontAwesome icon class)
        ],
        'error' => [
            'background' => 'bg-red-500',
            'icon' => 'times-circle', // Icon for error toasts
        ],
        'warning' => [
            'background' => 'bg-yellow-500',
            'icon' => 'exclamation-triangle', // Icon for warning toasts
        ],
        'info' => [
            'background' => 'bg-blue-500',
            'icon' => 'info-circle', // Icon for info toasts
        ],
    ],

    /*
     * Maximum Toasts
     * Define the maximum number of toasts that can be displayed at once.
     * Set to `null` for no limit.
     */
    'max_toasts' => 5,

    /*
     * Close Button
     * Customize the close button for toasts.
     */
    'close_button' => [
        // Whether to show the close button
        'enabled' => true,

        // Close button text or icon (e.g., '×', '✖', 'Close')
        'content' => '×',

        // Close button text color (e.g., 'text-white', 'text-black')
        'text_color' => 'text-white',

        // Close button hover effect (e.g., 'hover:bg-black/10')
        'hover_effect' => 'hover:bg-black/10',
    ],
];
