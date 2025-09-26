<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('survey_access_codes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('survey_id')->constrained()->onDelete('cascade');
            $table->string('access_code')->unique();
            $table->string('name')->nullable(); // Optional name for the access code
            $table->text('description')->nullable();
            $table->integer('max_uses')->nullable(); // Maximum number of uses (null = unlimited)
            $table->integer('used_count')->default(0);
            $table->timestamp('expires_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('survey_access_codes');
    }
};