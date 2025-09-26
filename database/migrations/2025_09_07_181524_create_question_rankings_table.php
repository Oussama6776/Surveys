<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('question_rankings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->constrained()->onDelete('cascade');
            $table->json('ranking_options'); // Array of options to rank
            $table->integer('min_rankings')->default(1); // Minimum number of items to rank
            $table->integer('max_rankings')->nullable(); // Maximum number of items to rank (null = all)
            $table->boolean('allow_ties')->default(false); // Allow same ranking for multiple items
            $table->string('ranking_direction')->default('desc'); // 'asc' or 'desc'
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('question_rankings');
    }
};