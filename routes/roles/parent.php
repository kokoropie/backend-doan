<?php
use App\Http\Controllers\Api\AcademicYearController;
use App\Http\Controllers\Api\ClassSubjectController;
use App\Http\Controllers\Api\Dashboard\ParentController;
use App\Http\Controllers\Api\FeedbackController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\UserScoreController;
use Illuminate\Support\Facades\Route;

Route::prefix('parent')->name('parent.')->middleware(['role:parent'])->group(function () {
    Route::prefix('dashboard')->name('dashboard.')->group(function () {
        Route::get('count', [ParentController::class, 'count'])->name('count');
    });

    Route::apiResource('academic-years', AcademicYearController::class)->only(['index', 'show']);
    Route::apiResource('feedback', FeedbackController::class);
    Route::apiResource('users', UserController::class)->only(['index', 'show']);
    Route::apiResource('classes.subjects', ClassSubjectController::class)->only(['index', 'show']);
    Route::apiResource('users.scores', UserScoreController::class)->only('index');
});