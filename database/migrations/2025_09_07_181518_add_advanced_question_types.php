<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Add new question types to existing question_types table
        DB::table('question_types')->insert([
            [
                'name' => 'Rating Scale',
                'description' => 'Rating scale from 1 to N',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Star Rating',
                'description' => 'Star rating system',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Ranking',
                'description' => 'Rank items in order of preference',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'File Upload',
                'description' => 'Upload files (images, documents, etc.)',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Location',
                'description' => 'Geographic location picker',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Date/Time',
                'description' => 'Date and time picker',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Email',
                'description' => 'Email address input with validation',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Phone',
                'description' => 'Phone number input with validation',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'URL',
                'description' => 'Website URL input with validation',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Number',
                'description' => 'Numeric input with validation',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Slider',
                'description' => 'Range slider for numeric values',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Matrix',
                'description' => 'Matrix of questions with common options',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }

    public function down()
    {
        // Remove the added question types
        DB::table('question_types')->whereIn('name', [
            'Rating Scale', 'Star Rating', 'Ranking', 'File Upload',
            'Location', 'Date/Time', 'Email', 'Phone', 'URL',
            'Number', 'Slider', 'Matrix'
        ])->delete();
    }
};