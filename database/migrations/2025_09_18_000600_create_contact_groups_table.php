<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('contact_groups')) {
            Schema::create('contact_groups', function (Blueprint $table) {
                $table->id();
                $table->foreignId('survey_id')->constrained()->onDelete('cascade');
                $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
                $table->string('name');
                $table->timestamps();
                $table->unique(['survey_id', 'created_by', 'name']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_groups');
    }
};
