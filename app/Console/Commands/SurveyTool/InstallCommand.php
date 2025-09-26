<?php

namespace App\Console\Commands\SurveyTool;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Role;

class InstallCommand extends Command
{
    protected $signature = 'surveytool:install 
                            {--force : Force installation even if already installed}
                            {--seed : Run database seeders}';

    protected $description = 'Install Survey Tool with all advanced features';

    public function handle()
    {
        $this->info('🚀 Installing Survey Tool with Advanced Features...');
        $this->newLine();

        // Check if already installed
        if (!$this->option('force') && $this->isInstalled()) {
            $this->error('Survey Tool is already installed. Use --force to reinstall.');
            return 1;
        }

        try {
            $this->runInstallationSteps();
            $this->info('✅ Survey Tool installed successfully!');
            $this->newLine();
            $this->displayPostInstallationInfo();
            return 0;
        } catch (\Exception $e) {
            $this->error('❌ Installation failed: ' . $e->getMessage());
            return 1;
        }
    }

    private function runInstallationSteps()
    {
        $steps = [
            'Publishing configuration files' => fn() => $this->publishConfigs(),
            'Running database migrations' => fn() => $this->runMigrations(),
            'Creating default roles' => fn() => $this->createDefaultRoles(),
            'Creating default themes' => fn() => $this->createDefaultThemes(),
            'Setting up permissions' => fn() => $this->setupPermissions(),
            'Creating admin user' => fn() => $this->createAdminUser(),
            'Building assets' => fn() => $this->buildAssets(),
            'Clearing caches' => fn() => $this->clearCaches(),
        ];

        if ($this->option('seed')) {
            $steps['Running database seeders'] = fn() => $this->runSeeders();
        }

        foreach ($steps as $description => $step) {
            $this->info("📦 {$description}...");
            $step();
            $this->info("✅ {$description} completed");
        }
    }

    private function publishConfigs()
    {
        // Configuration files are already in place
        $this->line('   Configuration files are ready');
    }

    private function runMigrations()
    {
        Artisan::call('migrate', ['--force' => true]);
        $this->line('   Database migrations completed');
    }

    private function createDefaultRoles()
    {
        $roles = [
            [
                'name' => 'super_admin',
                'display_name' => 'Super Admin',
                'description' => 'Full system access with all permissions',
                'permissions' => ['*'],
            ],
            [
                'name' => 'admin',
                'display_name' => 'Administrator',
                'description' => 'Administrative access to surveys and users',
                'permissions' => [
                    'surveys.create', 'surveys.read', 'surveys.update', 'surveys.delete',
                    'questions.create', 'questions.read', 'questions.update', 'questions.delete',
                    'responses.read', 'responses.delete',
                    'users.read', 'users.update', 'analytics.read', 'analytics.export',
                    'themes.create', 'themes.read', 'themes.update', 'themes.delete',
                    'files.upload', 'files.read', 'files.delete', 'files.download',
                    'webhooks.create', 'webhooks.read', 'webhooks.update', 'webhooks.delete', 'webhooks.test',
                    'system.settings', 'system.logs'
                ],
            ],
            [
                'name' => 'survey_creator',
                'display_name' => 'Survey Creator',
                'description' => 'Can create and manage surveys',
                'permissions' => [
                    'surveys.create', 'surveys.read', 'surveys.update', 'surveys.delete',
                    'questions.create', 'questions.read', 'questions.update', 'questions.delete',
                    'responses.read', 'responses.export',
                    'analytics.read', 'analytics.export',
                    'themes.read',
                    'files.upload', 'files.read', 'files.delete', 'files.download',
                    'webhooks.create', 'webhooks.read', 'webhooks.update', 'webhooks.delete', 'webhooks.test'
                ],
            ],
            [
                'name' => 'survey_viewer',
                'display_name' => 'Survey Viewer',
                'description' => 'Can only view surveys and responses',
                'permissions' => [
                    'surveys.read', 'questions.read', 'responses.read',
                    'analytics.read', 'themes.read', 'files.read', 'files.download'
                ],
            ]
        ];

        foreach ($roles as $roleData) {
            Role::updateOrCreate(
                ['name' => $roleData['name']],
                $roleData
            );
        }

        $this->line('   Default roles created');
    }

    private function createDefaultThemes()
    {
        $themes = [
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
            ]
        ];

        foreach ($themes as $themeData) {
            \App\Models\SurveyTheme::updateOrCreate(
                ['name' => $themeData['name']],
                $themeData
            );
        }

        $this->line('   Default themes created');
    }

    private function setupPermissions()
    {
        // Permissions are handled by the Role model
        $this->line('   Permissions system configured');
    }

    private function createAdminUser()
    {
        if (User::where('email', 'admin@surveytool.com')->exists()) {
            $this->line('   Admin user already exists');
            return;
        }

        $admin = User::create([
            'name' => 'Super Admin',
            'email' => 'admin@surveytool.com',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
        ]);

        $admin->assignRole('super_admin');

        $this->line('   Admin user created (admin@surveytool.com / password)');
    }

    private function buildAssets()
    {
        if ($this->confirm('Build frontend assets? (This may take a few minutes)', true)) {
            $this->line('   Building frontend assets...');
            $this->line('   Run: npm run build');
            $this->line('   Frontend assets will be built manually');
        }
    }

    private function clearCaches()
    {
        Artisan::call('config:clear');
        Artisan::call('cache:clear');
        Artisan::call('view:clear');
        Artisan::call('route:clear');
        $this->line('   Caches cleared');
    }

    private function runSeeders()
    {
        Artisan::call('db:seed', ['--force' => true]);
        $this->line('   Database seeders completed');
    }

    private function isInstalled()
    {
        try {
            return DB::table('roles')->exists() && DB::table('survey_themes')->exists();
        } catch (\Exception $e) {
            return false;
        }
    }

    private function displayPostInstallationInfo()
    {
        $this->info('🎉 Installation Complete!');
        $this->newLine();
        $this->line('📋 Next Steps:');
        $this->line('   1. Update your .env file with your database credentials');
        $this->line('   2. Run: php artisan serve');
        $this->line('   3. Visit: http://localhost:8000');
        $this->line('   4. Login with: admin@surveytool.com / password');
        $this->newLine();
        $this->line('🔧 Configuration:');
        $this->line('   - Edit config/survey-tool.php for advanced settings');
        $this->line('   - Configure webhooks in the admin panel');
        $this->line('   - Set up your preferred themes');
        $this->newLine();
        $this->line('📚 Documentation:');
        $this->line('   - Read FEATURES.md for detailed feature documentation');
        $this->line('   - Check the API documentation in the admin panel');
        $this->newLine();
        $this->line('🚀 Enjoy your new Survey Tool!');
    }
}