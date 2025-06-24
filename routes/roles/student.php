<?php
use App\Http\Controllers\Api\AcademicYearController;
use App\Http\Controllers\Api\ClassController;
use App\Http\Controllers\Api\ClassSubjectController;
use App\Http\Controllers\Api\Dashboard\StudentController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\UserScoreController;
use Illuminate\Support\Facades\Route;

Route::prefix('student')->name('student.')->middleware(['role:student'])->group(function () {
    Route::prefix('dashboard')->name('dashboard.')->group(function () {
        Route::get('count', [StudentController::class, 'count'])->name('count');
        Route::get('scores', [StudentController::class, 'scores'])->name('scores');
        Route::get('charts', [StudentController::class, 'charts'])->name('charts');
    });

    Route::apiResource('academic-years', AcademicYearController::class)->only(['index', 'show']);
    Route::apiResource('classes', ClassController::class)->only(['index', 'show']);
    Route::apiResource('classes.subjects', ClassSubjectController::class)->only(['index', 'show']);
    Route::apiResource('users.scores', UserScoreController::class)->only('index');
    Route::post('notifications/mark-as-read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-as-read');
    Route::apiResource('notifications', NotificationController::class)->only(['index', 'show', 'update']);
});