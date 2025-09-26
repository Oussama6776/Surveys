<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Survey Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration options for the survey system
    |
    */

    'default_theme' => env('SURVEY_DEFAULT_THEME', 'default'),
    
    'max_file_size' => env('SURVEY_MAX_FILE_SIZE', 10240), // KB
    
    'allowed_file_types' => [
        'image' => ['jpeg', 'jpg', 'png', 'gif', 'webp', 'svg'],
        'document' => ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt', 'csv'],
        'video' => ['mp4', 'avi', 'mov', 'wmv', 'webm'],
        'audio' => ['mp3', 'wav', 'ogg', 'mpeg'],
    ],
    
    'webhook_timeout' => env('SURVEY_WEBHOOK_TIMEOUT', 30),
    
    'webhook_retry_count' => env('SURVEY_WEBHOOK_RETRY_COUNT', 3),
    
    'analytics_cache_ttl' => env('SURVEY_ANALYTICS_CACHE_TTL', 3600), // seconds
    
    'api_rate_limit' => env('SURVEY_API_RATE_LIMIT', 100), // requests per minute
    
    'default_permissions' => [
        'surveys.create',
        'surveys.read',
        'surveys.update',
        'questions.create',
        'questions.read',
        'questions.update',
        'responses.read',
    ],
    
    'admin_permissions' => [
        'surveys.create',
        'surveys.read',
        'surveys.update',
        'surveys.delete',
        'surveys.publish',
        'questions.create',
        'questions.read',
        'questions.update',
        'questions.delete',
        'responses.read',
        'responses.delete',
        'responses.export',
        'users.read',
        'users.update',
        'analytics.read',
        'analytics.export',
        'themes.create',
        'themes.read',
        'themes.update',
        'themes.delete',
    ],
    
    'super_admin_permissions' => ['*'],
];
