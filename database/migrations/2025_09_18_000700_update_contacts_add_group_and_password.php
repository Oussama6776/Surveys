<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            if (!Schema::hasColumn('contacts', 'contact_group_id')) {
                $table->foreignId('contact_group_id')->nullable()->after('survey_id')
                    ->constrained('contact_groups')->nullOnDelete();
            }
            if (!Schema::hasColumn('contacts', 'created_by')) {
                $table->foreignId('created_by')->nullable()->after('contact_group_id')
                    ->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('contacts', 'password')) {
                $table->string('password')->nullable()->after('email');
            }
        });
        // Ajouter l'index composite si absent
        try {
            $conn = Schema::getConnection();
            $schema = $conn->getDoctrineSchemaManager();
            $details = $schema->listTableDetails('contacts');
            if (!$details->hasIndex('contacts_contact_group_id_email_index')) {
                Schema::table('contacts', function (Blueprint $table) {
                    $table->index(['contact_group_id', 'email']);
                });
            }
        } catch (\Throwable $e) {
            // Doctrine non installé: tenter de créer l'index et ignorer l'erreur si déjà présent
            try {
                Schema::table('contacts', function (Blueprint $table) {
                    $table->index(['contact_group_id', 'email']);
                });
            } catch (\Throwable $e2) {
                // ignore
            }
        }
    }

    public function down(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            try { $table->dropIndex('contacts_contact_group_id_email_index'); } catch (\Throwable $e) {}
            if (Schema::hasColumn('contacts', 'contact_group_id')) {
                try { $table->dropConstrainedForeignId('contact_group_id'); } catch (\Throwable $e) {}
            }
            if (Schema::hasColumn('contacts', 'created_by')) {
                try { $table->dropConstrainedForeignId('created_by'); } catch (\Throwable $e) {}
            }
            if (Schema::hasColumn('contacts', 'password')) {
                try { $table->dropColumn('password'); } catch (\Throwable $e) {}
            }
        });
    }
};
