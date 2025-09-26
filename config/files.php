<?php

return [
    /*
    |--------------------------------------------------------------------------
    | File Upload Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration options for file upload system
    |
    */

    'max_file_size' => env('FILE_MAX_SIZE', 10240), // KB
    
    'allowed_extensions' => [
        'image' => ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'],
        'document' => ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt', 'csv', 'rtf'],
        'video' => ['mp4', 'avi', 'mov', 'wmv', 'webm', 'flv'],
        'audio' => ['mp3', 'wav', 'ogg', 'mpeg', 'aac', 'flac'],
        'archive' => ['zip', 'rar', '7z', 'tar', 'gz'],
        'other' => ['*'] // Allow all file types
    ],
    
    'storage_disk' => env('FILE_STORAGE_DISK', 'local'),
    
    'storage_path' => env('FILE_STORAGE_PATH', 'surveys'),
    
    'public_storage' => env('FILE_PUBLIC_STORAGE', false),
    
    'virus_scan' => env('FILE_VIRUS_SCAN', false),
    
    'image_processing' => [
        'enabled' => env('FILE_IMAGE_PROCESSING', true),
        'max_width' => env('FILE_IMAGE_MAX_WIDTH', 1920),
        'max_height' => env('FILE_IMAGE_MAX_HEIGHT', 1080),
        'quality' => env('FILE_IMAGE_QUALITY', 85),
        'thumbnails' => [
            'enabled' => true,
            'sizes' => [
                'small' => [150, 150],
                'medium' => [300, 300],
                'large' => [600, 600]
            ]
        ]
    ],
    
    'cleanup' => [
        'enabled' => env('FILE_CLEANUP_ENABLED', true),
        'orphaned_files_ttl' => env('FILE_ORPHANED_TTL', 86400), // 24 hours
        'temp_files_ttl' => env('FILE_TEMP_TTL', 3600), // 1 hour
    ],
    
    'security' => [
        'scan_uploads' => env('FILE_SCAN_UPLOADS', false),
        'quarantine_suspicious' => env('FILE_QUARANTINE_SUSPICIOUS', false),
        'allowed_mime_types' => [
            'image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml',
            'application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'text/plain', 'text/csv', 'video/mp4', 'video/avi', 'video/mov',
            'audio/mp3', 'audio/wav', 'audio/ogg', 'application/zip'
        ]
    ],
    
    'cdn' => [
        'enabled' => env('FILE_CDN_ENABLED', false),
        'url' => env('FILE_CDN_URL'),
        'key' => env('FILE_CDN_KEY'),
        'secret' => env('FILE_CDN_SECRET'),
        'region' => env('FILE_CDN_REGION'),
        'bucket' => env('FILE_CDN_BUCKET')
    ]
];
