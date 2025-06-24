<?php
use App\Http\Controllers\Api\AcademicYearController;
use App\Http\Controllers\Api\ClassController;
use App\Http\Controllers\Api\ClassScoreController;
use App\Http\Controllers\Api\ClassSubjectController;
use App\Http\Controllers\Api\Dashboard\TeacherController;
use App\Http\Controllers\Api\FeedbackController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\ScoreController;
use App\Http\Controllers\Api\SemesterController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\UserScoreController;
use Illuminate\Support\Facades\Route;

Route::prefix('teacher')->name('teacher.')->middleware(['role:teacher'])->group(function () {
    Route::prefix('dashboard')->name('dashboard.')->group(function () {
        Route::get('count', [TeacherController::class, 'count'])->name('count');
        Route::get('feedback', [TeacherController::class, 'feedback'])->name('feedback');
    });

    Route::apiResource('academic-years', AcademicYearController::class)->only(['index', 'show']);

    Route::apiResource('academic-years.semesters', SemesterController::class)->only(['index', 'show']);

    Route::apiResource('classes', ClassController::class)->only(['index', 'show']);

    Route::apiResource('classes.subjects', ClassSubjectController::class)->only(['index', 'show']);
    Route::apiResource('classes.scores', ClassScoreController::class);

    Route::apiResource('users', UserController::class)->only(['index', 'show']);
    Route::apiResource('scores', ScoreController::class);
    Route::apiResource('users.scores', UserScoreController::class)->only('index');

    Route::apiResource('feedback', FeedbackController::class)->except(['store']);
    Route::post('notifications/mark-as-read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-as-read');
    Route::apiResource('notifications', NotificationController::class);
});