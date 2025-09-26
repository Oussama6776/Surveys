<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('responses') || !Schema::hasColumn('responses', 'contact_id')) {
            return; // rien à faire si la colonne n'existe pas
        }

        // Dédupliquer: garder la réponse la plus récente par (survey_id, contact_id)
        $duplicates = DB::table('responses')
            ->select('survey_id', 'contact_id', DB::raw('COUNT(*) as cnt'))
            ->whereNotNull('contact_id')
            ->groupBy('survey_id', 'contact_id')
            ->having('cnt', '>', 1)
            ->get();

        foreach ($duplicates as $dup) {
            $rows = DB::table('responses')
                ->where('survey_id', $dup->survey_id)
                ->where('contact_id', $dup->contact_id)
                ->orderByDesc('submitted_at')
                ->orderByDesc('id')
                ->get();

            $keep = $rows->first();
            $toDelete = $rows->skip(1);
            if ($keep && $toDelete->count() > 0) {
                $ids = $toDelete->pluck('id')->all();
                // Supprimer d'abord les answers orphelines
                DB::table('answers')->whereIn('response_id', $ids)->delete();
                // Supprimer les réponses en doublon
                DB::table('responses')->whereIn('id', $ids)->delete();
            }
        }

        // Ajouter contrainte d'unicité
        Schema::table('responses', function (Blueprint $table) {
            $table->unique(['survey_id', 'contact_id'], 'responses_survey_contact_unique');
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('responses') || !Schema::hasColumn('responses', 'contact_id')) {
            return;
        }
        Schema::table('responses', function (Blueprint $table) {
            // Supprimer l'index si présent
            try {
                $table->dropUnique('responses_survey_contact_unique');
            } catch (\Throwable $e) {
                // ignore
            }
        });
    }
};
