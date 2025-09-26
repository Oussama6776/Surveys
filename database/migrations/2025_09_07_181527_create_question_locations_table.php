<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('question_locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->constrained()->onDelete('cascade');
            $table->string('location_type')->default('point'); // 'point', 'area', 'route'
            $table->decimal('default_latitude', 10, 8)->nullable();
            $table->decimal('default_longitude', 11, 8)->nullable();
            $table->integer('default_zoom')->default(10);
            $table->json('allowed_countries')->nullable(); // Restrict to specific countries
            $table->json('restricted_areas')->nullable(); // Areas where location is not allowed
            $table->boolean('require_precise_location')->default(false);
            $table->boolean('show_map')->default(true);
            $table->boolean('allow_search')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('question_locations');
    }
};