<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\User;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        // 1. SUPER ADMIN - Accès complet au système
        $superAdmin = Role::updateOrCreate(
            ['name' => 'super_admin'],
            [
                'display_name' => 'Super Administrateur',
                'description' => 'Accès complet au système avec toutes les permissions',
                'permissions' => ['*'] // Accès à tout
            ]
        );

        // 2. ADMIN - Gestion des utilisateurs et du système
        $admin = Role::updateOrCreate(
            ['name' => 'admin'],
            [
                'display_name' => 'Administrateur',
                'description' => 'Gestion des utilisateurs, enquêtes et configuration système',
                'permissions' => [
                    // Gestion des utilisateurs
                    'users.create', 'users.read', 'users.update', 'users.delete',
                    'users.assign_roles', 'users.manage_permissions',
                    
                    // Gestion des enquêtes
                    'surveys.create', 'surveys.read', 'surveys.update', 'surveys.delete',
                    'surveys.publish', 'surveys.archive', 'surveys.duplicate',
                    
                    // Gestion des questions
                    'questions.create', 'questions.read', 'questions.update', 'questions.delete',
                    'questions.reorder', 'questions.duplicate',
                    
                    // Gestion des réponses
                    'responses.read', 'responses.delete', 'responses.export',
                    'responses.analytics', 'responses.export_all',
                    
                    // Analytics et rapports
                    'analytics.read', 'analytics.export', 'analytics.dashboard',
                    'analytics.reports', 'analytics.real_time',
                    
                    // Gestion des thèmes
                    'themes.create', 'themes.read', 'themes.update', 'themes.delete',
                    'themes.duplicate', 'themes.export', 'themes.import',
                    
                    // Gestion des fichiers
                    'files.upload', 'files.read', 'files.delete', 'files.download',
                    'files.manage_all', 'files.cleanup',
                    
                    // Gestion des webhooks
                    'webhooks.create', 'webhooks.read', 'webhooks.update', 'webhooks.delete',
                    'webhooks.test', 'webhooks.logs',
                    
                    // Gestion des rôles
                    'roles.create', 'roles.read', 'roles.update', 'roles.delete',
                    'roles.assign', 'roles.permissions',
                    
                    // Configuration système
                    'system.settings', 'system.logs', 'system.maintenance',
                    'system.backup', 'system.cache'
                ]
            ]
        );

        // 3. SURVEY_CREATOR - Créateur d'enquêtes
        $surveyCreator = Role::updateOrCreate(
            ['name' => 'survey_creator'],
            [
                'display_name' => 'Créateur d\'Enquêtes',
                'description' => 'Peut créer et gérer ses propres enquêtes',
                'permissions' => [
                    // Gestion de ses propres enquêtes
                    'surveys.create', 'surveys.read_own', 'surveys.update_own', 'surveys.delete_own',
                    'surveys.publish_own', 'surveys.archive_own', 'surveys.duplicate_own',
                    
                    // Gestion des questions de ses enquêtes
                    'questions.create', 'questions.read_own', 'questions.update_own', 'questions.delete_own',
                    'questions.reorder_own', 'questions.duplicate_own',
                    
                    // Gestion des réponses de ses enquêtes
                    'responses.read_own', 'responses.export_own', 'responses.analytics_own',
                    
                    // Analytics de ses enquêtes
                    'analytics.read_own', 'analytics.export_own', 'analytics.dashboard_own',
                    
                    // Utilisation des thèmes
                    'themes.read', 'themes.use',
                    
                    // Gestion des fichiers de ses enquêtes
                    'files.upload', 'files.read_own', 'files.delete_own', 'files.download_own',
                    
                    // Gestion des webhooks de ses enquêtes
                    'webhooks.create_own', 'webhooks.read_own', 'webhooks.update_own', 'webhooks.delete_own',
                    'webhooks.test_own'
                ]
            ]
        );

        // 4. SURVEY_VIEWER - Lecteur d'enquêtes
        $surveyViewer = Role::updateOrCreate(
            ['name' => 'survey_viewer'],
            [
                'display_name' => 'Lecteur d\'Enquêtes',
                'description' => 'Peut seulement consulter les enquêtes et réponses',
                'permissions' => [
                    // Lecture seule des enquêtes
                    'surveys.read', 'questions.read', 'responses.read',
                    
                    // Analytics en lecture seule
                    'analytics.read', 'analytics.dashboard',
                    
                    // Utilisation des thèmes
                    'themes.read', 'themes.use',
                    
                    // Téléchargement des fichiers
                    'files.read', 'files.download'
                ]
            ]
        );

        // 5. CLIENT - Client final (répond aux enquêtes)
        $client = Role::updateOrCreate(
            ['name' => 'client'],
            [
                'display_name' => 'Client',
                'description' => 'Peut répondre aux enquêtes publiques',
                'permissions' => [
                    // Répondre aux enquêtes
                    'surveys.respond', 'surveys.view_public',
                    
                    // Télécharger des fichiers nécessaires
                    'files.download_public'
                ]
            ]
        );

        // 6. MODERATOR - Modérateur de contenu
        $moderator = Role::updateOrCreate(
            ['name' => 'moderator'],
            [
                'display_name' => 'Modérateur',
                'description' => 'Peut modérer le contenu des enquêtes',
                'permissions' => [
                    // Lecture des enquêtes
                    'surveys.read', 'questions.read', 'responses.read',
                    
                    // Modération
                    'surveys.moderate', 'questions.moderate', 'responses.moderate',
                    
                    // Analytics
                    'analytics.read', 'analytics.dashboard',
                    
                    // Fichiers
                    'files.read', 'files.download'
                ]
            ]
        );

        $this->command->info('✅ Rôles créés avec succès :');
        $this->command->line('   - Super Admin (accès complet)');
        $this->command->line('   - Admin (gestion système)');
        $this->command->line('   - Créateur d\'Enquêtes (ses enquêtes)');
        $this->command->line('   - Lecteur d\'Enquêtes (lecture seule)');
        $this->command->line('   - Client (répond aux enquêtes)');
        $this->command->line('   - Modérateur (modération contenu)');
    }
}
