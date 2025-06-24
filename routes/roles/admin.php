<?php
use App\Http\Controllers\Api\AcademicYearController;
use App\Http\Controllers\Api\ClassController;
use App\Http\Controllers\Api\ClassScoreController;
use App\Http\Controllers\Api\ClassSubjectController;
use App\Http\Controllers\Api\Dashboard\AdminController;
use App\Http\Controllers\Api\SemesterController;
use App\Http\Controllers\Api\SubjectController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\UserScoreController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->middleware(['role:admin'])->group(function () {
    Route::prefix('dashboard')->name('dashboard.')->group(function () {
        Route::get('count', [AdminController::class, 'count'])->name('count');
        Route::get('top-score', [AdminController::class, 'topScore'])->name('top-score');
    });
    Route::apiResource('academic-years', AcademicYearController::class);
    Route::post('academic-years/{academicYear}/set-year', [AcademicYearController::class, 'setYear'])->name('academic-years.set-year');

    Route::apiResource('academic-years.semesters', SemesterController::class);

    Route::apiResource('classes', ClassController::class);

    Route::apiResource('classes.subjects', ClassSubjectController::class);
    Route::post('classes/{class}/subjects/copy', [ClassSubjectController::class, 'copy'])->name('classes.subjects.copy');
    Route::apiResource('classes.scores', ClassScoreController::class);

    Route::apiResource('subjects', SubjectController::class);

    Route::apiResource('users', UserController::class);
    Route::put('users/{user}/password', [UserController::class, 'changePassword'])->name('users.change-password');
    Route::post('users/{user}/class', [UserController::class, 'changeClass'])->name('users.change-class');

    Route::apiResource('users.scores', UserScoreController::class)->only('index');
});