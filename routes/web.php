<?php

use Illuminate\Support\Facades\Route;
use App\Models\Survey;
use App\Http\Controllers\SurveyController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\ResponseController;
use App\Http\Controllers\DashboardController;

// Welcome page route
Route::get('/', function () {
    $surveys = Survey::where('is_public', true)
        ->latest()
        ->get();
    return view('welcome', compact('surveys'));
})->name('welcome');

// Authentication Routes
Route::get('login', [App\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [App\Http\Controllers\Auth\LoginController::class, 'login']);
Route::post('logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');
Route::get('register', [App\Http\Controllers\Auth\RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('register', [App\Http\Controllers\Auth\RegisterController::class, 'register']);

// Public Survey Routes (no auth required)
Route::get('survey/{survey}', [SurveyController::class, 'publicShow'])->name('survey.public.show');
// Public response submission (no auth)
Route::post('survey/{survey}/responses', [App\Http\Controllers\ResponseController::class, 'store'])->name('responses.public.store');

// Public surveys listing page
Route::get('/surveys/public', function () {
    $surveys = \App\Models\Survey::where('is_public', true)->latest()->paginate(12);
    return view('surveys.public.index', compact('surveys'));
})->name('surveys.public.index');
Route::get('thank-you', function () {
    return view('survey.thank-you');
})->name('survey.thank-you');

// Public routes for contacts and invitations
// Show/respond to a survey (guarded by CheckSurveyAccess for private cases)
Route::get('/surveys/{survey}/respond', [SurveyController::class, 'respond'])
    ->middleware(\App\Http\Middleware\CheckSurveyAccess::class)
    ->name('surveys.respond');

// Contact authentication (private surveys) — guest-accessible
Route::get('/surveys/{survey}/contacts/login', [App\Http\Controllers\ContactAuthController::class, 'showLoginForm'])->name('contacts.login.form');
Route::post('/surveys/{survey}/contacts/login', [App\Http\Controllers\ContactAuthController::class, 'login'])->name('contacts.login.attempt');
Route::post('/surveys/{survey}/contacts/logout', [App\Http\Controllers\ContactAuthController::class, 'logout'])->name('contacts.logout');

// Invitation token access (contact-only, per survey) — public
Route::get('/surveys/invite/{token}', [App\Http\Controllers\ContactAuthController::class, 'accessByToken'])->name('surveys.invite.access');

// Protected Routes (require authentication)
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Route de test pour vérifier l'accès admin
    Route::get('/test-admin', function() {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['error' => 'Non connecté']);
        }
        
        return response()->json([
            'user' => $user->name,
            'email' => $user->email,
            'roles' => $user->roles->pluck('display_name'),
            'hasRole_super_admin' => $user->hasRole('super_admin'),
            'hasRole_admin' => $user->hasRole('admin'),
            'hasPermission_users_read' => $user->hasPermission('users.read'),
            'hasPermission_users_create' => $user->hasPermission('users.create'),
        ]);
    });

    // Survey Routes
    Route::prefix('surveys')->group(function () {
        Route::get('/', [SurveyController::class, 'index'])->name('surveys.index');
        Route::get('/create', [SurveyController::class, 'create'])->name('surveys.create');
        Route::post('/', [SurveyController::class, 'store'])->name('surveys.store');
        Route::get('/{survey}', [SurveyController::class, 'show'])->name('surveys.show');
        Route::get('/{survey}/edit', [SurveyController::class, 'edit'])->name('surveys.edit');
        Route::put('/{survey}', [SurveyController::class, 'update'])->name('surveys.update');
        Route::delete('/{survey}', [SurveyController::class, 'destroy'])->name('surveys.destroy');
        Route::get('/{survey}/results', [SurveyController::class, 'results'])->name('surveys.results');
        Route::post('/{survey}/publish', [SurveyController::class, 'publish'])->name('surveys.publish');
        Route::post('/{survey}/unpublish', [SurveyController::class, 'unpublish'])->name('surveys.unpublish');

        // Contacts import & listing (creator/admin only)
        Route::get('/{survey}/contacts', [App\Http\Controllers\ContactController::class, 'index'])->name('surveys.contacts.index');
        Route::post('/{survey}/contacts/import', [App\Http\Controllers\ContactController::class, 'import'])->name('surveys.contacts.import');
        Route::get('/{survey}/contacts/template', [App\Http\Controllers\ContactController::class, 'template'])->name('surveys.contacts.template');
        Route::get('/{survey}/contacts/export', [App\Http\Controllers\ContactController::class, 'export'])->name('surveys.contacts.export');
        Route::post('/{survey}/contacts/send', [App\Http\Controllers\ContactController::class, 'sendFromGroup'])->name('surveys.contacts.send');
    });

    // Question Routes
    Route::prefix('surveys/{survey}')->scopeBindings()->group(function () {
        Route::get('questions/create', [QuestionController::class, 'create'])->name('questions.create');
        Route::post('questions', [QuestionController::class, 'store'])->name('questions.store');
        Route::get('questions/{question}/edit', [QuestionController::class, 'edit'])->name('questions.edit');
        Route::put('questions/{question}', [QuestionController::class, 'update'])->name('questions.update');
        Route::delete('questions/{question}', [QuestionController::class, 'destroy'])->name('questions.destroy');
        // Ajout en lot de questions (bulk)
        Route::post('questions/bulk', [QuestionController::class, 'store'])->name('questions.bulk.store');
        Route::post('questions/reorder', [QuestionController::class, 'reorder'])->name('questions.reorder');
    });

    // Response Routes
    Route::prefix('surveys/{survey}')->group(function () {
        Route::resource('responses', ResponseController::class)->only(['index', 'show']);
        Route::post('respond', [ResponseController::class, 'store'])->name('responses.store');
    });

    // Analytics Routes
    Route::prefix('analytics')->group(function () {
        Route::get('/', [App\Http\Controllers\AnalyticsController::class, 'index'])->name('analytics.index');
        Route::get('/surveys/{survey}', [App\Http\Controllers\AnalyticsController::class, 'show'])->name('analytics.show');
        Route::get('/surveys/{survey}/export/pdf', [App\Http\Controllers\AnalyticsController::class, 'exportPdf'])->name('analytics.export.pdf');
        Route::get('/surveys/{survey}/export/excel', [App\Http\Controllers\AnalyticsController::class, 'exportExcel'])->name('analytics.export.excel');
    });

    // Theme Routes
    Route::prefix('themes')->group(function () {
        Route::get('/', [App\Http\Controllers\ThemeController::class, 'index'])->name('themes.index');
        Route::get('/create', [App\Http\Controllers\ThemeController::class, 'create'])->name('themes.create');
        Route::post('/', [App\Http\Controllers\ThemeController::class, 'store'])->name('themes.store');
        Route::get('/{theme}', [App\Http\Controllers\ThemeController::class, 'show'])->name('themes.show');
        Route::get('/{theme}/edit', [App\Http\Controllers\ThemeController::class, 'edit'])->name('themes.edit');
        Route::put('/{theme}', [App\Http\Controllers\ThemeController::class, 'update'])->name('themes.update');
        Route::delete('/{theme}', [App\Http\Controllers\ThemeController::class, 'destroy'])->name('themes.destroy');
        Route::get('/{theme}/preview', [App\Http\Controllers\ThemeController::class, 'preview'])->name('themes.preview');
        Route::post('/{theme}/duplicate', [App\Http\Controllers\ThemeController::class, 'duplicate'])->name('themes.duplicate');
        Route::get('/{theme}/export', [App\Http\Controllers\ThemeController::class, 'export'])->name('themes.export');
        Route::post('/import', [App\Http\Controllers\ThemeController::class, 'import'])->name('themes.import');
    });

    // Role Management Routes
    Route::prefix('roles')->group(function () {
        Route::get('/', [App\Http\Controllers\RoleController::class, 'index'])->name('roles.index');
        Route::get('/create', [App\Http\Controllers\RoleController::class, 'create'])->name('roles.create');
        Route::post('/', [App\Http\Controllers\RoleController::class, 'store'])->name('roles.store');
        Route::get('/{role}', [App\Http\Controllers\RoleController::class, 'show'])->name('roles.show');
        Route::get('/{role}/edit', [App\Http\Controllers\RoleController::class, 'edit'])->name('roles.edit');
        Route::put('/{role}', [App\Http\Controllers\RoleController::class, 'update'])->name('roles.update');
        Route::delete('/{role}', [App\Http\Controllers\RoleController::class, 'destroy'])->name('roles.destroy');
        Route::post('/{role}/assign-user', [App\Http\Controllers\RoleController::class, 'assignUser'])->name('roles.assign-user');
        Route::delete('/{role}/users/{user}', [App\Http\Controllers\RoleController::class, 'removeUser'])->name('roles.remove-user');
    });

    // Webhook Routes
    Route::prefix('surveys/{survey}/webhooks')->group(function () {
        Route::get('/', [App\Http\Controllers\WebhookController::class, 'index'])->name('webhooks.index');
        Route::get('/create', [App\Http\Controllers\WebhookController::class, 'create'])->name('webhooks.create');
        Route::post('/', [App\Http\Controllers\WebhookController::class, 'store'])->name('webhooks.store');
        Route::get('/{webhook}', [App\Http\Controllers\WebhookController::class, 'show'])->name('webhooks.show');
        Route::get('/{webhook}/edit', [App\Http\Controllers\WebhookController::class, 'edit'])->name('webhooks.edit');
        Route::put('/{webhook}', [App\Http\Controllers\WebhookController::class, 'update'])->name('webhooks.update');
        Route::delete('/{webhook}', [App\Http\Controllers\WebhookController::class, 'destroy'])->name('webhooks.destroy');
        Route::post('/{webhook}/test', [App\Http\Controllers\WebhookController::class, 'test'])->name('webhooks.test');
        Route::post('/{webhook}/toggle', [App\Http\Controllers\WebhookController::class, 'toggle'])->name('webhooks.toggle');
        Route::get('/{webhook}/logs', [App\Http\Controllers\WebhookController::class, 'logs'])->name('webhooks.logs');
    });

    // File Management Routes
    Route::prefix('surveys/{survey}/files')->group(function () {
        Route::get('/', [App\Http\Controllers\FileController::class, 'index'])->name('files.index');
        Route::post('/upload', [App\Http\Controllers\FileController::class, 'upload'])->name('files.upload');
        Route::get('/export', [App\Http\Controllers\FileController::class, 'export'])->name('files.export');
        Route::post('/bulk-delete', [App\Http\Controllers\FileController::class, 'bulkDelete'])->name('files.bulk-delete');
    });

    // File Access Routes (public)
    Route::get('/files/{file}/download', [App\Http\Controllers\FileController::class, 'download'])->name('files.download');
    Route::get('/files/{file}/view', [App\Http\Controllers\FileController::class, 'view'])->name('files.view');
    Route::delete('/files/{file}', [App\Http\Controllers\FileController::class, 'delete'])->name('files.delete');
    
    // User Management Routes
    Route::prefix('users')->group(function () {
        Route::get('/', [App\Http\Controllers\UserController::class, 'index'])->name('users.index');
        Route::get('/create', [App\Http\Controllers\UserController::class, 'create'])->name('users.create');
        Route::post('/', [App\Http\Controllers\UserController::class, 'store'])->name('users.store');
        Route::get('/{user}', [App\Http\Controllers\UserController::class, 'show'])->name('users.show');
        Route::get('/{user}/edit', [App\Http\Controllers\UserController::class, 'edit'])->name('users.edit');
        Route::put('/{user}', [App\Http\Controllers\UserController::class, 'update'])->name('users.update');
        Route::delete('/{user}', [App\Http\Controllers\UserController::class, 'destroy'])->name('users.destroy');
        Route::post('/{user}/roles', [App\Http\Controllers\UserController::class, 'assignRole'])->name('users.assign_role');
        Route::delete('/{user}/roles/{role}', [App\Http\Controllers\UserController::class, 'removeRole'])->name('users.remove_role');
        Route::post('/{user}/reset-password', [App\Http\Controllers\UserController::class, 'resetPassword'])->name('users.reset_password');
    });
    
    // Profile Routes
    Route::get('/profile', [App\Http\Controllers\UserController::class, 'profile'])->name('profile');
    Route::put('/profile', [App\Http\Controllers\UserController::class, 'updateProfile'])->name('profile.update');
});

// Survey Access Routes (public)
Route::get('/surveys/{survey}/access-code', [App\Http\Controllers\SurveyController::class, 'showAccessCodeForm'])->name('surveys.access-code');
Route::post('/surveys/{survey}/verify-access-code', [App\Http\Controllers\SurveyController::class, 'verifyAccessCode'])->name('surveys.verify-access-code');

// API Routes
Route::prefix('api/v1')->group(function () {
    // Public API Routes
    Route::get('/surveys', [App\Http\Controllers\Api\SurveyApiController::class, 'index']);
    Route::get('/surveys/{survey}', [App\Http\Controllers\Api\SurveyApiController::class, 'show']);
    Route::get('/surveys/{survey}/public', [App\Http\Controllers\Api\SurveyApiController::class, 'publicShow']);
    Route::post('/surveys/{survey}/responses', [App\Http\Controllers\Api\ResponseApiController::class, 'store']);

    // Protected API Routes
    Route::middleware(['auth:sanctum'])->group(function () {
        // Survey Management
        Route::post('/surveys', [App\Http\Controllers\Api\SurveyApiController::class, 'store']);
        Route::put('/surveys/{survey}', [App\Http\Controllers\Api\SurveyApiController::class, 'update']);
        Route::delete('/surveys/{survey}', [App\Http\Controllers\Api\SurveyApiController::class, 'destroy']);
        Route::post('/surveys/{survey}/publish', [App\Http\Controllers\Api\SurveyApiController::class, 'publish']);
        Route::post('/surveys/{survey}/unpublish', [App\Http\Controllers\Api\SurveyApiController::class, 'unpublish']);
        Route::post('/surveys/{survey}/duplicate', [App\Http\Controllers\Api\SurveyApiController::class, 'duplicate']);
        Route::get('/surveys/{survey}/stats', [App\Http\Controllers\Api\SurveyApiController::class, 'stats']);

        // Response Management
        Route::get('/surveys/{survey}/responses', [App\Http\Controllers\Api\ResponseApiController::class, 'index']);
        Route::get('/surveys/{survey}/responses/{response}', [App\Http\Controllers\Api\ResponseApiController::class, 'show']);
        Route::put('/surveys/{survey}/responses/{response}', [App\Http\Controllers\Api\ResponseApiController::class, 'update']);
        Route::delete('/surveys/{survey}/responses/{response}', [App\Http\Controllers\Api\ResponseApiController::class, 'destroy']);
        Route::get('/surveys/{survey}/responses/export', [App\Http\Controllers\Api\ResponseApiController::class, 'export']);

        // Analytics
        Route::get('/surveys/{survey}/analytics/overview', [App\Http\Controllers\Api\AnalyticsApiController::class, 'overview']);
        Route::get('/surveys/{survey}/analytics/questions', [App\Http\Controllers\Api\AnalyticsApiController::class, 'questionAnalytics']);
        Route::get('/surveys/{survey}/analytics/export', [App\Http\Controllers\Api\AnalyticsApiController::class, 'export']);
    });
});
