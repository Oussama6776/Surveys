<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('contacts') || !Schema::hasColumn('contacts', 'contact_group_id')) {
            return;
        }

        // Dédupliquer par (survey_id, contact_group_id, email) en gardant le plus récent
        $dups = DB::table('contacts')
            ->select('survey_id', 'contact_group_id', 'email', DB::raw('COUNT(*) as cnt'))
            ->groupBy('survey_id', 'contact_group_id', 'email')
            ->having('cnt', '>', 1)
            ->get();

        foreach ($dups as $d) {
            $rows = DB::table('contacts')
                ->where('survey_id', $d->survey_id)
                ->where('contact_group_id', $d->contact_group_id)
                ->where('email', $d->email)
                ->orderByDesc('id')
                ->get();
            $keep = $rows->first();
            $toDelete = $rows->skip(1);
            if ($keep && $toDelete->count() > 0) {
                $ids = $toDelete->pluck('id')->all();
                // Nettoyage des éventuelles relations
                DB::table('survey_invite_tokens')->whereIn('contact_id', $ids)->delete();
                DB::table('contacts')->whereIn('id', $ids)->delete();
            }
        }

        // Ajouter contrainte d'unicité
        Schema::table('contacts', function (Blueprint $table) {
            // Pour MySQL, la longueur de varchar(191) convient pour l'index
            $table->unique(['survey_id', 'contact_group_id', 'email'], 'contacts_survey_group_email_unique');
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('contacts')) return;
        Schema::table('contacts', function (Blueprint $table) {
            try { $table->dropUnique('contacts_survey_group_email_unique'); } catch (\Throwable $e) {}
        });
    }
};

