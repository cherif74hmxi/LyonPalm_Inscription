<?php

$isLocal = env('APP_ENV') === 'local';

return [
    'headers' => [
        'content_security_policy' => [
            'enabled' => env('SECURITY_CSP_ENABLED', true),
            'directives' => [
                'default-src' => ["'self'"],
                'base-uri' => ["'self'"],
                'connect-src' => array_filter([
                    "'self'",
                    $isLocal ? 'http://localhost:*' : null,
                    $isLocal ? 'http://127.0.0.1:*' : null,
                    $isLocal ? 'ws://localhost:*' : null,
                    $isLocal ? 'ws://127.0.0.1:*' : null,
                ]),
                'font-src' => ["'self'", 'data:'],
                'form-action' => ["'self'"],
                'frame-ancestors' => ["'none'"],
                'frame-src' => ["'none'"],
                'img-src' => ["'self'", 'data:', 'blob:'],
                'manifest-src' => ["'self'"],
                'media-src' => ["'self'"],
                'object-src' => ["'none'"],
                'script-src' => array_filter([
                    "'self'",
                    $isLocal ? 'http://localhost:*' : null,
                    $isLocal ? 'http://127.0.0.1:*' : null,
                ]),
                'style-src' => array_filter([
                    "'self'",
                    $isLocal ? "'unsafe-inline'" : null,
                    $isLocal ? 'http://localhost:*' : null,
                    $isLocal ? 'http://127.0.0.1:*' : null,
                ]),
            ],
        ],

        'cross_origin_embedder_policy' => env(
            'SECURITY_CROSS_ORIGIN_EMBEDDER_POLICY',
            $isLocal ? null : 'require-corp',
        ),
        'cross_origin_opener_policy' => env('SECURITY_CROSS_ORIGIN_OPENER_POLICY', 'same-origin'),
        'cross_origin_resource_policy' => env('SECURITY_CROSS_ORIGIN_RESOURCE_POLICY', 'same-origin'),
        'permissions_policy' => env(
            'SECURITY_PERMISSIONS_POLICY',
            'accelerometer=(), autoplay=(), camera=(), encrypted-media=(), fullscreen=(self), geolocation=(), gyroscope=(), magnetometer=(), microphone=(), payment=(), usb=()',
        ),
        'referrer_policy' => env('SECURITY_REFERRER_POLICY', 'strict-origin-when-cross-origin'),
        'x_content_type_options' => env('SECURITY_X_CONTENT_TYPE_OPTIONS', 'nosniff'),
        'x_frame_options' => env('SECURITY_X_FRAME_OPTIONS', 'DENY'),

        'strict_transport_security' => [
            'enabled' => env('SECURITY_HSTS_ENABLED', true),
            'max_age' => (int) env('SECURITY_HSTS_MAX_AGE', 2592000),
            'include_subdomains' => env('SECURITY_HSTS_INCLUDE_SUBDOMAINS', false),
            'preload' => env('SECURITY_HSTS_PRELOAD', false),
        ],
    ],

    'cache' => [
        'private' => env('SECURITY_PRIVATE_CACHE_CONTROL', 'no-store, no-cache, must-revalidate, private'),
    ],

    'login' => [
        'max_attempts' => (int) env('LOGIN_MAX_ATTEMPTS', 5),
        'decay_seconds' => (int) env('LOGIN_DECAY_SECONDS', 60),
    ],
];
