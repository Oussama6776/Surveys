<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('survey_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('survey_id')->constrained()->onDelete('cascade');
            $table->foreignId('response_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('question_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('original_name');
            $table->string('file_name'); // Stored file name
            $table->string('file_path');
            $table->string('mime_type');
            $table->unsignedBigInteger('file_size'); // in bytes
            $table->string('file_type'); // 'image', 'document', 'video', 'audio', 'other'
            $table->boolean('is_public')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('survey_files');
    }
};