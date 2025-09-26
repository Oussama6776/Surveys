<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\SurveyTheme;
use App\Models\Role;

class SurveyServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Share themes with all views
        View::composer('*', function ($view) {
            $view->with('availableThemes', SurveyTheme::where('is_active', true)->get());
        });

        // Share roles with admin views
        View::composer(['roles.*', 'users.*'], function ($view) {
            $view->with('availableRoles', Role::all());
        });

        // Register custom validation rules
        $this->registerValidationRules();
    }

    private function registerValidationRules()
    {
        // Custom validation rules can be added here
        // For example, survey access code validation, etc.
    }
}