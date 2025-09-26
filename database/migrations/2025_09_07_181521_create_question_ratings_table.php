<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('question_ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->constrained()->onDelete('cascade');
            $table->integer('min_value')->default(1);
            $table->integer('max_value')->default(5);
            $table->string('min_label')->nullable(); // e.g., "Poor"
            $table->string('max_label')->nullable(); // e.g., "Excellent"
            $table->string('rating_type')->default('numeric'); // 'numeric', 'stars', 'smileys', 'thumbs'
            $table->boolean('show_labels')->default(true);
            $table->boolean('allow_half_ratings')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('question_ratings');
    }
};