<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('survey_invite_tokens')) {
            Schema::create('survey_invite_tokens', function (Blueprint $table) {
                $table->id();
                $table->foreignId('survey_id')->constrained()->onDelete('cascade');
                $table->foreignId('contact_id')->constrained('contacts')->onDelete('cascade');
                $table->string('token', 128)->unique();
                $table->timestamp('expires_at')->nullable();
                $table->timestamp('used_at')->nullable();
                $table->timestamps();
                $table->index(['survey_id', 'contact_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('survey_invite_tokens');
    }
};
