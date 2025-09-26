<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::create('survey_themes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('display_name');
            $table->text('description')->nullable();
            $table->string('primary_color', 7)->default('#007bff'); // Hex color
            $table->string('secondary_color', 7)->default('#6c757d');
            $table->string('background_color', 7)->default('#ffffff');
            $table->string('text_color', 7)->default('#333333');
            $table->string('font_family')->default('Arial, sans-serif');
            $table->integer('font_size')->default(16);
            $table->json('custom_css')->nullable(); // Additional CSS rules
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Insert default themes
        DB::table('survey_themes')->insert([
            [
                'name' => 'default',
                'display_name' => 'Default Theme',
                'description' => 'Clean and professional default theme',
                'primary_color' => '#007bff',
                'secondary_color' => '#6c757d',
                'background_color' => '#ffffff',
                'text_color' => '#333333',
                'font_family' => 'Arial, sans-serif',
                'font_size' => 16,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'dark',
                'display_name' => 'Dark Theme',
                'description' => 'Modern dark theme for better readability',
                'primary_color' => '#0d6efd',
                'secondary_color' => '#6c757d',
                'background_color' => '#212529',
                'text_color' => '#ffffff',
                'font_family' => 'Arial, sans-serif',
                'font_size' => 16,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'corporate',
                'display_name' => 'Corporate Theme',
                'description' => 'Professional corporate theme',
                'primary_color' => '#1e3a8a',
                'secondary_color' => '#64748b',
                'background_color' => '#f8fafc',
                'text_color' => '#1e293b',
                'font_family' => 'Georgia, serif',
                'font_size' => 16,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'colorful',
                'display_name' => 'Colorful Theme',
                'description' => 'Vibrant and engaging theme',
                'primary_color' => '#e91e63',
                'secondary_color' => '#ff9800',
                'background_color' => '#f3e5f5',
                'text_color' => '#4a148c',
                'font_family' => 'Comic Sans MS, cursive',
                'font_size' => 16,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('survey_themes');
    }
};