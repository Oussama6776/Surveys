<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('display_name');
            $table->text('description')->nullable();
            $table->json('permissions')->nullable(); // Store permissions as JSON
            $table->timestamps();
        });

        // Insert default roles
        DB::table('roles')->insert([
            [
                'name' => 'super_admin',
                'display_name' => 'Super Admin',
                'description' => 'Full system access with all permissions',
                'permissions' => json_encode(['*']),
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'admin',
                'display_name' => 'Administrator',
                'description' => 'Administrative access to surveys and users',
                'permissions' => json_encode([
                    'surveys.create', 'surveys.read', 'surveys.update', 'surveys.delete',
                    'questions.create', 'questions.read', 'questions.update', 'questions.delete',
                    'responses.read', 'responses.delete',
                    'users.read', 'users.update'
                ]),
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'survey_creator',
                'display_name' => 'Survey Creator',
                'description' => 'Can create and manage surveys',
                'permissions' => json_encode([
                    'surveys.create', 'surveys.read', 'surveys.update', 'surveys.delete',
                    'questions.create', 'questions.read', 'questions.update', 'questions.delete',
                    'responses.read'
                ]),
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'survey_viewer',
                'display_name' => 'Survey Viewer',
                'description' => 'Can only view surveys and responses',
                'permissions' => json_encode([
                    'surveys.read', 'questions.read', 'responses.read'
                ]),
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('roles');
    }
};