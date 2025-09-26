<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('webhooks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('survey_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('url');
            $table->string('event_type'); // 'response_submitted', 'survey_completed', 'survey_created', etc.
            $table->string('http_method')->default('POST');
            $table->json('headers')->nullable(); // Custom headers
            $table->json('payload_template')->nullable(); // Custom payload structure
            $table->boolean('is_active')->default(true);
            $table->integer('retry_count')->default(3);
            $table->integer('timeout')->default(30); // seconds
            $table->timestamp('last_triggered_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('webhooks');
    }
};