<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('surveys', function (Blueprint $table) {
            // Theme and styling
            $table->foreignId('theme_id')->nullable()->constrained('survey_themes')->onDelete('set null');
            $table->json('custom_styling')->nullable(); // Custom CSS overrides
            
            // Access control
            $table->boolean('is_public')->default(true);
            $table->boolean('requires_access_code')->default(false);
            $table->boolean('allow_multiple_responses')->default(true);
            $table->integer('max_responses')->nullable(); // Limit total responses
            $table->integer('max_responses_per_user')->default(1); // Limit per user/IP
            
            // Survey behavior
            $table->boolean('show_progress_bar')->default(true);
            $table->boolean('randomize_questions')->default(false);
            $table->boolean('randomize_options')->default(false);
            $table->boolean('allow_back_navigation')->default(true);
            $table->boolean('save_progress')->default(false);
            
            // Multi-page support
            $table->boolean('is_multi_page')->default(false);
            $table->integer('questions_per_page')->default(10);
            
            // Completion settings
            $table->text('completion_message')->nullable();
            $table->string('redirect_url')->nullable();
            $table->boolean('send_completion_email')->default(false);
            $table->string('completion_email_subject')->nullable();
            $table->text('completion_email_template')->nullable();
            
            // Analytics and tracking
            $table->boolean('track_analytics')->default(true);
            $table->boolean('collect_ip_address')->default(false);
            $table->boolean('collect_user_agent')->default(false);
            
            // Status and metadata
            $table->enum('status', ['draft', 'active', 'paused', 'completed'])->default('draft');
            $table->json('metadata')->nullable(); // Additional custom data
        });
    }

    public function down()
    {
        Schema::table('surveys', function (Blueprint $table) {
            $table->dropForeign(['theme_id']);
            $table->dropColumn([
                'theme_id', 'custom_styling', 'is_public', 'requires_access_code',
                'allow_multiple_responses', 'max_responses', 'max_responses_per_user',
                'show_progress_bar', 'randomize_questions', 'randomize_options',
                'allow_back_navigation', 'save_progress', 'is_multi_page',
                'questions_per_page', 'completion_message', 'redirect_url',
                'send_completion_email', 'completion_email_subject', 'completion_email_template',
                'track_analytics', 'collect_ip_address', 'collect_user_agent',
                'status', 'metadata'
            ]);
        });
    }
};