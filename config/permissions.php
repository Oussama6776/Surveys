<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Permissions Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration options for permission system
    |
    */

    'cache_permissions' => env('PERMISSIONS_CACHE', true),
    
    'cache_ttl' => env('PERMISSIONS_CACHE_TTL', 3600), // seconds
    
    'default_role' => env('PERMISSIONS_DEFAULT_ROLE', 'survey_creator'),
    
    'super_admin_role' => env('PERMISSIONS_SUPER_ADMIN_ROLE', 'super_admin'),
    
    'permissions' => [
        // Survey permissions
        'surveys.create' => 'Create Surveys',
        'surveys.read' => 'View Surveys',
        'surveys.update' => 'Edit Surveys',
        'surveys.delete' => 'Delete Surveys',
        'surveys.publish' => 'Publish Surveys',
        'surveys.duplicate' => 'Duplicate Surveys',
        
        // Question permissions
        'questions.create' => 'Create Questions',
        'questions.read' => 'View Questions',
        'questions.update' => 'Edit Questions',
        'questions.delete' => 'Delete Questions',
        'questions.reorder' => 'Reorder Questions',
        
        // Response permissions
        'responses.read' => 'View Responses',
        'responses.update' => 'Edit Responses',
        'responses.delete' => 'Delete Responses',
        'responses.export' => 'Export Responses',
        'responses.import' => 'Import Responses',
        
        // User permissions
        'users.create' => 'Create Users',
        'users.read' => 'View Users',
        'users.update' => 'Edit Users',
        'users.delete' => 'Delete Users',
        'users.manage' => 'Manage User Roles',
        'users.impersonate' => 'Impersonate Users',
        
        // Analytics permissions
        'analytics.read' => 'View Analytics',
        'analytics.export' => 'Export Analytics',
        'analytics.reports' => 'Generate Reports',
        
        // Theme permissions
        'themes.create' => 'Create Themes',
        'themes.read' => 'View Themes',
        'themes.update' => 'Edit Themes',
        'themes.delete' => 'Delete Themes',
        'themes.import' => 'Import Themes',
        'themes.export' => 'Export Themes',
        
        // File permissions
        'files.upload' => 'Upload Files',
        'files.read' => 'View Files',
        'files.delete' => 'Delete Files',
        'files.download' => 'Download Files',
        
        // Webhook permissions
        'webhooks.create' => 'Create Webhooks',
        'webhooks.read' => 'View Webhooks',
        'webhooks.update' => 'Edit Webhooks',
        'webhooks.delete' => 'Delete Webhooks',
        'webhooks.test' => 'Test Webhooks',
        
        // System permissions
        'system.settings' => 'Manage System Settings',
        'system.backup' => 'Backup System',
        'system.restore' => 'Restore System',
        'system.logs' => 'View System Logs',
        'system.maintenance' => 'Maintenance Mode',
        
        // API permissions
        'api.access' => 'API Access',
        'api.manage' => 'Manage API Keys',
        'api.analytics' => 'API Analytics',
    ],
    
    'role_permissions' => [
        'super_admin' => ['*'],
        'admin' => [
            'surveys.create', 'surveys.read', 'surveys.update', 'surveys.delete', 'surveys.publish',
            'questions.create', 'questions.read', 'questions.update', 'questions.delete',
            'responses.read', 'responses.delete', 'responses.export',
            'users.read', 'users.update', 'users.manage',
            'analytics.read', 'analytics.export',
            'themes.create', 'themes.read', 'themes.update', 'themes.delete',
            'files.upload', 'files.read', 'files.delete', 'files.download',
            'webhooks.create', 'webhooks.read', 'webhooks.update', 'webhooks.delete', 'webhooks.test',
            'system.settings', 'system.logs'
        ],
        'survey_creator' => [
            'surveys.create', 'surveys.read', 'surveys.update', 'surveys.delete', 'surveys.publish',
            'questions.create', 'questions.read', 'questions.update', 'questions.delete',
            'responses.read', 'responses.export',
            'analytics.read', 'analytics.export',
            'themes.read',
            'files.upload', 'files.read', 'files.delete', 'files.download',
            'webhooks.create', 'webhooks.read', 'webhooks.update', 'webhooks.delete', 'webhooks.test'
        ],
        'survey_viewer' => [
            'surveys.read',
            'questions.read',
            'responses.read',
            'analytics.read',
            'themes.read',
            'files.read', 'files.download'
        ]
    ],
    
    'permission_groups' => [
        'Surveys' => [
            'surveys.create', 'surveys.read', 'surveys.update', 'surveys.delete', 'surveys.publish', 'surveys.duplicate'
        ],
        'Questions' => [
            'questions.create', 'questions.read', 'questions.update', 'questions.delete', 'questions.reorder'
        ],
        'Responses' => [
            'responses.read', 'responses.update', 'responses.delete', 'responses.export', 'responses.import'
        ],
        'Users' => [
            'users.create', 'users.read', 'users.update', 'users.delete', 'users.manage', 'users.impersonate'
        ],
        'Analytics' => [
            'analytics.read', 'analytics.export', 'analytics.reports'
        ],
        'Themes' => [
            'themes.create', 'themes.read', 'themes.update', 'themes.delete', 'themes.import', 'themes.export'
        ],
        'Files' => [
            'files.upload', 'files.read', 'files.delete', 'files.download'
        ],
        'Webhooks' => [
            'webhooks.create', 'webhooks.read', 'webhooks.update', 'webhooks.delete', 'webhooks.test'
        ],
        'System' => [
            'system.settings', 'system.backup', 'system.restore', 'system.logs', 'system.maintenance'
        ],
        'API' => [
            'api.access', 'api.manage', 'api.analytics'
        ]
    ]
];
