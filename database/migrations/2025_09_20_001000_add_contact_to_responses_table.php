<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('responses', function (Blueprint $table) {
            if (!Schema::hasColumn('responses', 'contact_id')) {
                $table->foreignId('contact_id')->nullable()->after('survey_id')
                    ->constrained('contacts')->nullOnDelete();
            }
            // Index pour recherches (non unique pour compatibilité)
            if (!Schema::hasColumn('responses', 'contact_id')) return; // guard
        });
        // Ajout d'un index composite (ignorer s'il existe déjà)
        try {
            Schema::table('responses', function (Blueprint $table) {
                $table->index(['survey_id', 'contact_id'], 'responses_survey_contact_idx');
            });
        } catch (\Throwable $e) {
            // ignore if already exists
        }
    }

    public function down(): void
    {
        Schema::table('responses', function (Blueprint $table) {
            if (Schema::hasColumn('responses', 'contact_id')) {
                $table->dropIndex('responses_survey_contact_idx');
                $table->dropConstrainedForeignId('contact_id');
            }
        });
    }
};
