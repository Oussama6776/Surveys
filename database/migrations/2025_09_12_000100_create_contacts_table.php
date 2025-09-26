<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('survey_id')->constrained()->onDelete('cascade');
            $table->string('nom');
            $table->string('prenom');
            $table->string('email')->index();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('status_envoi', ['envoye', 'en_attente', 'echoue'])->default('en_attente');
            $table->timestamp('date_envoi')->nullable();
            $table->timestamps();
            $table->unique(['survey_id', 'email']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('contacts');
    }
};

