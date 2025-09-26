<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->string('event'); // 'created', 'updated', 'deleted', 'viewed', 'exported'
            $table->string('auditable_type'); // Model class name
            $table->unsignedBigInteger('auditable_id'); // Model ID
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->json('old_values')->nullable(); // Previous values
            $table->json('new_values')->nullable(); // New values
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->string('url')->nullable();
            $table->timestamps();
            
            $table->index(['auditable_type', 'auditable_id']);
            $table->index(['user_id', 'created_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('audit_logs');
    }
};