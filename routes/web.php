<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SurveyController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\ResponseController;
use App\Http\Controllers\DashboardController;

// Welcome page route
Route::get('/', function () {
    return view('welcome');
})->name('welcome');

// Authentication Routes
Route::get('login', [App\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [App\Http\Controllers\Auth\LoginController::class, 'login']);
Route::post('logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');
Route::get('register', [App\Http\Controllers\Auth\RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('register', [App\Http\Controllers\Auth\RegisterController::class, 'register']);

// Public Survey Routes (no auth required)
Route::get('survey/{survey}', [SurveyController::class, 'publicShow'])->name('survey.public.show');
Route::get('thank-you', function () {
    return view('survey.thank-you');
})->name('survey.thank-you');

// Protected Routes (require authentication)
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

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
    });

    // Question Routes
    Route::prefix('surveys/{survey}')->group(function () {
        Route::resource('questions', QuestionController::class)->except(['index', 'show']);
    });

    // Response Routes
    Route::prefix('surveys/{survey}')->group(function () {
        Route::resource('responses', ResponseController::class)->only(['index', 'show']);
        Route::post('respond', [ResponseController::class, 'store'])->name('responses.store');
    });
});
