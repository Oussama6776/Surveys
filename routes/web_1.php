<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SurveyController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\ParticipationController;
use App\Http\Controllers\ResponseController;
use App\Http\Controllers\StatsController;
use App\Http\Controllers\ExportController;

Route::get('/', function () {
    return redirect()->route('surveys.index');
})->middleware(['auth']);

Route::middleware(['auth'])->group(function () {
    Route::get('/surveys', [SurveyController::class, 'index'])->name('surveys.index');
    Route::get('/surveys/create', [SurveyController::class, 'create'])->name('surveys.create');
    Route::post('/surveys', [SurveyController::class, 'store'])->name('surveys.store');
    Route::get('/surveys/{survey}', [SurveyController::class, 'show'])->name('surveys.show');
    Route::get('/surveys/{survey}/edit', [SurveyController::class, 'edit'])->name('surveys.edit');
    Route::put('/surveys/{survey}', [SurveyController::class, 'update'])->name('surveys.update');
    Route::delete('/surveys/{survey}', [SurveyController::class, 'destroy'])->name('surveys.destroy');

    Route::post('/surveys/{survey}/questions', [QuestionController::class, 'store'])->name('questions.store');
    Route::put('/questions/{question}', [QuestionController::class, 'update'])->name('questions.update');
    Route::delete('/questions/{question}', [QuestionController::class, 'destroy'])->name('questions.destroy');

    Route::get('/surveys/{survey}/responses', [ResponseController::class, 'index'])->name('responses.index');
    Route::get('/responses/{response}', [ResponseController::class, 'show'])->name('responses.show');

    Route::get('/surveys/{survey}/stats', [StatsController::class, 'show'])->name('stats.show');
    Route::get('/surveys/{survey}/export.csv', [ExportController::class, 'csv'])->name('export.csv');
});

Route::get('/p/{public_token}', [ParticipationController::class, 'show'])->name('participation.show');
Route::post('/p/{public_token}', [ParticipationController::class, 'submit'])->name('participation.submit');
Route::get('/merci', [ParticipationController::class, 'thanks'])->name('participation.thanks');

require __DIR__.'/auth.php';

