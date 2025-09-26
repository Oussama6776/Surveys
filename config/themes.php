<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Theme Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration options for theme system
    |
    */

    'default_theme' => env('THEME_DEFAULT', 'default'),
    
    'cache_themes' => env('THEME_CACHE', true),
    
    'cache_ttl' => env('THEME_CACHE_TTL', 3600), // seconds
    
    'custom_css_allowed' => env('THEME_CUSTOM_CSS_ALLOWED', true),
    
    'preview_mode' => env('THEME_PREVIEW_MODE', false),
    
    'color_palettes' => [
        'primary' => [
            '#007bff', '#6c757d', '#28a745', '#dc3545', '#ffc107',
            '#17a2b8', '#6f42c1', '#e83e8c', '#fd7e14', '#20c997'
        ],
        'secondary' => [
            '#6c757d', '#495057', '#6c757d', '#6c757d', '#6c757d',
            '#6c757d', '#6c757d', '#6c757d', '#6c757d', '#6c757d'
        ],
        'background' => [
            '#ffffff', '#f8f9fa', '#e9ecef', '#dee2e6', '#ced4da',
            '#adb5bd', '#6c757d', '#495057', '#343a40', '#212529'
        ],
        'text' => [
            '#212529', '#495057', '#6c757d', '#868e96', '#adb5bd',
            '#ced4da', '#dee2e6', '#e9ecef', '#f8f9fa', '#ffffff'
        ]
    ],
    
    'font_families' => [
        'Arial, sans-serif',
        'Helvetica, sans-serif',
        'Georgia, serif',
        'Times New Roman, serif',
        'Courier New, monospace',
        'Verdana, sans-serif',
        'Tahoma, sans-serif',
        'Trebuchet MS, sans-serif',
        'Comic Sans MS, cursive',
        'Impact, sans-serif'
    ],
    
    'font_sizes' => [
        12, 14, 16, 18, 20, 22, 24, 26, 28, 30, 32, 34, 36
    ],
    
    'preview_survey' => [
        'title' => 'Sample Survey',
        'description' => 'This is a preview of how your survey will look with the selected theme.',
        'questions' => [
            [
                'text' => 'What is your favorite color?',
                'type' => 'Multiple Choice',
                'options' => ['Red', 'Blue', 'Green', 'Yellow']
            ],
            [
                'text' => 'Please rate our service',
                'type' => 'Rating Scale',
                'options' => ['1', '2', '3', '4', '5']
            ],
            [
                'text' => 'Any additional comments?',
                'type' => 'Text',
                'options' => []
            ]
        ]
    ]
];
