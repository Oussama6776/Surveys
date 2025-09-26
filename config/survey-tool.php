<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Survey Tool Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration options for the Survey Tool application
    |
    */

    'version' => '2.0.0',
    
    'features' => [
        'analytics' => env('SURVEY_ANALYTICS_ENABLED', true),
        'themes' => env('SURVEY_THEMES_ENABLED', true),
        'webhooks' => env('SURVEY_WEBHOOKS_ENABLED', true),
        'file_upload' => env('SURVEY_FILE_UPLOAD_ENABLED', true),
        'conditional_logic' => env('SURVEY_CONDITIONAL_LOGIC_ENABLED', true),
        'multi_page' => env('SURVEY_MULTI_PAGE_ENABLED', true),
        'role_management' => env('SURVEY_ROLE_MANAGEMENT_ENABLED', true),
        'api' => env('SURVEY_API_ENABLED', true),
    ],

    'defaults' => [
        'theme' => env('SURVEY_DEFAULT_THEME', 'default'),
        'max_file_size' => env('SURVEY_MAX_FILE_SIZE', 10240), // KB
        'questions_per_page' => env('SURVEY_QUESTIONS_PER_PAGE', 10),
        'max_responses' => env('SURVEY_MAX_RESPONSES', null),
        'completion_timeout' => env('SURVEY_COMPLETION_TIMEOUT', 3600), // seconds
    ],

    'security' => [
        'access_codes' => [
            'enabled' => env('SURVEY_ACCESS_CODES_ENABLED', true),
            'length' => env('SURVEY_ACCESS_CODE_LENGTH', 8),
            'expiration' => env('SURVEY_ACCESS_CODE_EXPIRATION', 86400), // seconds
        ],
        'rate_limiting' => [
            'enabled' => env('SURVEY_RATE_LIMITING_ENABLED', true),
            'max_attempts' => env('SURVEY_RATE_LIMIT_MAX_ATTEMPTS', 10),
            'decay_minutes' => env('SURVEY_RATE_LIMIT_DECAY_MINUTES', 1),
        ],
        'ip_restrictions' => [
            'enabled' => env('SURVEY_IP_RESTRICTIONS_ENABLED', false),
            'whitelist' => explode(',', env('SURVEY_IP_WHITELIST', '')),
            'blacklist' => explode(',', env('SURVEY_IP_BLACKLIST', '')),
        ],
    ],

    'notifications' => [
        'email' => [
            'enabled' => env('SURVEY_EMAIL_NOTIFICATIONS_ENABLED', true),
            'from_address' => env('SURVEY_EMAIL_FROM_ADDRESS', 'noreply@surveytool.com'),
            'from_name' => env('SURVEY_EMAIL_FROM_NAME', 'Survey Tool'),
        ],
        'webhooks' => [
            'enabled' => env('SURVEY_WEBHOOK_NOTIFICATIONS_ENABLED', true),
            'timeout' => env('SURVEY_WEBHOOK_TIMEOUT', 30),
            'retry_count' => env('SURVEY_WEBHOOK_RETRY_COUNT', 3),
            'retry_delay' => env('SURVEY_WEBHOOK_RETRY_DELAY', 5),
        ],
    ],

    'integrations' => [
        'google_analytics' => [
            'enabled' => env('SURVEY_GOOGLE_ANALYTICS_ENABLED', false),
            'tracking_id' => env('SURVEY_GOOGLE_ANALYTICS_TRACKING_ID'),
        ],
        'google_maps' => [
            'enabled' => env('SURVEY_GOOGLE_MAPS_ENABLED', false),
            'api_key' => env('SURVEY_GOOGLE_MAPS_API_KEY'),
        ],
        'stripe' => [
            'enabled' => env('SURVEY_STRIPE_ENABLED', false),
            'public_key' => env('SURVEY_STRIPE_PUBLIC_KEY'),
            'secret_key' => env('SURVEY_STRIPE_SECRET_KEY'),
        ],
    ],

    'question_types' => [
        'text' => [
            'enabled' => true,
            'max_length' => 1000,
            'validation' => ['required', 'string', 'max:1000'],
        ],
        'textarea' => [
            'enabled' => true,
            'max_length' => 5000,
            'validation' => ['required', 'string', 'max:5000'],
        ],
        'multiple_choice' => [
            'enabled' => true,
            'max_options' => 20,
            'validation' => ['required', 'exists:question_options,id'],
        ],
        'checkbox' => [
            'enabled' => true,
            'max_options' => 20,
            'validation' => ['required', 'array'],
        ],
        'rating_scale' => [
            'enabled' => true,
            'min_value' => 1,
            'max_value' => 10,
            'validation' => ['required', 'integer', 'min:1', 'max:10'],
        ],
        'star_rating' => [
            'enabled' => true,
            'max_stars' => 5,
            'allow_half' => false,
            'validation' => ['required', 'numeric', 'min:0.5', 'max:5'],
        ],
        'ranking' => [
            'enabled' => true,
            'max_items' => 20,
            'validation' => ['required', 'array'],
        ],
        'file_upload' => [
            'enabled' => true,
            'max_size' => 10240, // KB
            'allowed_types' => ['image', 'document', 'video', 'audio'],
            'validation' => ['required', 'file', 'max:10240'],
        ],
        'location' => [
            'enabled' => true,
            'precision' => 'approximate', // exact, approximate
            'validation' => ['required', 'array'],
        ],
        'date_time' => [
            'enabled' => true,
            'format' => 'Y-m-d H:i:s',
            'validation' => ['required', 'date'],
        ],
        'email' => [
            'enabled' => true,
            'validation' => ['required', 'email'],
        ],
        'phone' => [
            'enabled' => true,
            'format' => 'international',
            'validation' => ['required', 'regex:/^[\+]?[1-9][\d]{0,15}$/'],
        ],
        'url' => [
            'enabled' => true,
            'validation' => ['required', 'url'],
        ],
        'number' => [
            'enabled' => true,
            'min' => null,
            'max' => null,
            'validation' => ['required', 'numeric'],
        ],
        'slider' => [
            'enabled' => true,
            'min' => 0,
            'max' => 100,
            'step' => 1,
            'validation' => ['required', 'numeric'],
        ],
        'matrix' => [
            'enabled' => true,
            'max_rows' => 20,
            'max_columns' => 10,
            'validation' => ['required', 'array'],
        ],
    ],

    'export' => [
        'formats' => ['csv', 'excel', 'pdf', 'json'],
        'csv' => [
            'delimiter' => ',',
            'enclosure' => '"',
            'escape' => '\\',
        ],
        'excel' => [
            'sheet_name' => 'Survey Data',
            'auto_size_columns' => true,
        ],
        'pdf' => [
            'page_size' => 'A4',
            'orientation' => 'portrait',
            'margins' => ['top' => 20, 'right' => 20, 'bottom' => 20, 'left' => 20],
        ],
    ],

    'cache' => [
        'enabled' => env('SURVEY_CACHE_ENABLED', true),
        'ttl' => env('SURVEY_CACHE_TTL', 3600), // seconds
        'prefix' => 'survey_tool:',
    ],

    'queue' => [
        'connection' => env('SURVEY_QUEUE_CONNECTION', 'database'),
        'queue' => env('SURVEY_QUEUE_NAME', 'default'),
    ],

    'storage' => [
        'disk' => env('SURVEY_STORAGE_DISK', 'local'),
        'path' => env('SURVEY_STORAGE_PATH', 'surveys'),
        'public' => env('SURVEY_STORAGE_PUBLIC', false),
    ],

    'logging' => [
        'enabled' => env('SURVEY_LOGGING_ENABLED', true),
        'level' => env('SURVEY_LOG_LEVEL', 'info'),
        'channels' => [
            'analytics' => env('SURVEY_ANALYTICS_LOG_CHANNEL', 'daily'),
            'webhooks' => env('SURVEY_WEBHOOKS_LOG_CHANNEL', 'daily'),
            'audit' => env('SURVEY_AUDIT_LOG_CHANNEL', 'daily'),
        ],
    ],

    'maintenance' => [
        'enabled' => env('SURVEY_MAINTENANCE_ENABLED', false),
        'message' => env('SURVEY_MAINTENANCE_MESSAGE', 'Survey Tool is currently under maintenance. Please try again later.'),
        'allowed_ips' => explode(',', env('SURVEY_MAINTENANCE_ALLOWED_IPS', '')),
    ],
];
