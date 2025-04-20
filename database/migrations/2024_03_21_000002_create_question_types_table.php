<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::create('question_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->nullable();
            $table->timestamps();
        });

        // Insert default question types
        DB::table('question_types')->insert([
            ['name' => 'Text', 'description' => 'Short text answer'],
            ['name' => 'Multiple Choice', 'description' => 'Single choice from multiple options'],
            ['name' => 'Checkbox', 'description' => 'Multiple choices from options'],
            ['name' => 'Textarea', 'description' => 'Long text answer'],
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('question_types');
    }
}; 