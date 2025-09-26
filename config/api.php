<?php

return [
    /*
    |--------------------------------------------------------------------------
    | API Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration options for the API system
    |
    */

    'version' => env('API_VERSION', 'v1'),
    
    'rate_limit' => env('API_RATE_LIMIT', 100), // requests per minute
    
    'api_key' => env('API_KEY', 'your-secret-api-key'),
    
    'sanctum_expiration' => env('SANCTUM_EXPIRATION', 525600), // minutes (1 year)
    
    'cors' => [
        'allowed_origins' => explode(',', env('CORS_ALLOWED_ORIGINS', '*')),
        'allowed_methods' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'],
        'allowed_headers' => ['Content-Type', 'Authorization', 'X-Requested-With', 'X-API-Key'],
        'exposed_headers' => ['X-RateLimit-Limit', 'X-RateLimit-Remaining'],
        'max_age' => 86400, // 24 hours
    ],
    
    'pagination' => [
        'default_per_page' => 15,
        'max_per_page' => 100,
    ],
    
    'response_format' => [
        'success_key' => 'success',
        'data_key' => 'data',
        'message_key' => 'message',
        'error_key' => 'error',
    ],
];
