<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Analytics Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration options for analytics system
    |
    */

    'cache_enabled' => env('ANALYTICS_CACHE_ENABLED', true),
    
    'cache_ttl' => env('ANALYTICS_CACHE_TTL', 3600), // seconds
    
    'real_time_enabled' => env('ANALYTICS_REAL_TIME_ENABLED', false),
    
    'export_formats' => ['csv', 'excel', 'pdf', 'json'],
    
    'chart_types' => [
        'bar' => 'Bar Chart',
        'line' => 'Line Chart',
        'pie' => 'Pie Chart',
        'doughnut' => 'Doughnut Chart',
        'radar' => 'Radar Chart',
        'polar' => 'Polar Area Chart'
    ],
    
    'default_chart_colors' => [
        '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF',
        '#FF9F40', '#FF6384', '#C9CBCF', '#4BC0C0', '#FF6384'
    ],
    
    'word_cloud' => [
        'max_words' => 50,
        'min_font_size' => 12,
        'max_font_size' => 48,
        'exclude_words' => ['the', 'and', 'or', 'but', 'in', 'on', 'at', 'to', 'for', 'of', 'with', 'by']
    ],
    
    'trend_analysis' => [
        'default_period' => 'day', // day, week, month, year
        'max_periods' => 365,
        'smoothing_factor' => 0.3
    ],
    
    'demographics' => [
        'track_device_types' => true,
        'track_browsers' => true,
        'track_locations' => false, // Requires IP geolocation service
        'track_referrers' => true,
        'track_time_of_day' => true
    ],
    
    'export' => [
        'pdf' => [
            'page_size' => 'A4',
            'orientation' => 'portrait',
            'margins' => ['top' => 20, 'right' => 20, 'bottom' => 20, 'left' => 20],
            'header' => true,
            'footer' => true
        ],
        'excel' => [
            'sheet_name' => 'Survey Analytics',
            'auto_size_columns' => true,
            'freeze_panes' => true
        ],
        'csv' => [
            'delimiter' => ',',
            'enclosure' => '"',
            'escape' => '\\'
        ]
    ],
    
    'notifications' => [
        'email_reports' => env('ANALYTICS_EMAIL_REPORTS', false),
        'report_frequency' => 'weekly', // daily, weekly, monthly
        'report_recipients' => explode(',', env('ANALYTICS_REPORT_RECIPIENTS', ''))
    ]
];