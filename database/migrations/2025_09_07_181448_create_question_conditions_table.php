<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('question_conditions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->constrained()->onDelete('cascade');
            $table->foreignId('depends_on_question_id')->constrained('questions')->onDelete('cascade');
            $table->string('condition_type'); // 'equals', 'not_equals', 'contains', 'greater_than', 'less_than', 'in', 'not_in'
            $table->text('condition_value'); // The value to compare against
            $table->string('action'); // 'show', 'hide', 'require', 'optional'
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('question_conditions');
    }
};