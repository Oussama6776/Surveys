<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('survey_pages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('survey_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('page_number');
            $table->boolean('is_active')->default(true);
            $table->json('page_settings')->nullable(); // Store page-specific settings
            $table->timestamps();
            
            $table->unique(['survey_id', 'page_number']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('survey_pages');
    }
};