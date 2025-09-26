<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('contacts')) return;
        Schema::table('contacts', function (Blueprint $table) {
            // Supprimer l'unicité (survey_id, email) pour autoriser le même email dans plusieurs groupes du même sondage
            try {
                $table->dropUnique('contacts_survey_id_email_unique');
            } catch (\Throwable $e) {
                // Index déjà supprimé ou inexistant
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('contacts')) return;
        Schema::table('contacts', function (Blueprint $table) {
            // Restaurer l'unicité si besoin
            try {
                $table->unique(['survey_id', 'email']);
            } catch (\Throwable $e) {
                // Ignorer si conflit avec d'autres contraintes
            }
        });
    }
};

