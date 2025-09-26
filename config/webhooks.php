<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Webhook Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration options for webhook system
    |
    */

    'default_timeout' => env('WEBHOOK_DEFAULT_TIMEOUT', 30),
    
    'default_retry_count' => env('WEBHOOK_DEFAULT_RETRY_COUNT', 3),
    
    'retry_delay' => env('WEBHOOK_RETRY_DELAY', 5), // seconds
    
    'max_payload_size' => env('WEBHOOK_MAX_PAYLOAD_SIZE', 1024), // KB
    
    'queue' => env('WEBHOOK_QUEUE', 'default'),
    
    'events' => [
        'response_submitted' => 'Response Submitted',
        'survey_completed' => 'Survey Completed',
        'survey_created' => 'Survey Created',
        'survey_updated' => 'Survey Updated',
        'survey_published' => 'Survey Published',
        'survey_closed' => 'Survey Closed',
        'response_deleted' => 'Response Deleted',
        'question_added' => 'Question Added',
        'question_updated' => 'Question Updated',
        'question_deleted' => 'Question Deleted',
    ],
    
    'default_headers' => [
        'Content-Type' => 'application/json',
        'User-Agent' => 'SurveyTool-Webhook/1.0',
    ],
    
    'signature_header' => 'X-SurveyTool-Signature',
    
    'secret' => env('WEBHOOK_SECRET', 'your-webhook-secret'),
];
