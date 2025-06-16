<?php

use App\Http\Controllers\Api\AcademicYearController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ClassController;
use App\Http\Controllers\Api\ClassScoreController;
use App\Http\Controllers\Api\ClassSubjectController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\SemesterController;
use App\Http\Controllers\Api\SubjectController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::name('api.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::post('auth/login', [AuthController::class, 'login'])->name('auth.login');
    });

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('profile', [ProfileController::class, 'index'])->name('profile');

        Route::delete('/auth/logout', [AuthController::class, 'logout'])->name('auth.logout');

        $files = glob(__DIR__ . '/roles/*.php');
        foreach ($files as $file) {
            require_once $file;
        }
    });

    Route::get('classes/{class}/excel', [ClassScoreController::class, 'excel'])->name('classes.scores.excel');

    Route::get('routes', function () {
        return response()->json(collect(Route::getRoutes())->map(function ($route) {
            return [
                'uri' => Str::after($route->uri, 'api/'),
                'name' => $route->getName(),
            ];
        })->filter(function ($route) {
            return \Str::startsWith($route['name'], 'api.');
        })->mapWithKeys(function ($route) {
            return [$route['name'] => $route['uri']];
        })->sortKeys());
    })->name('routes');
});

Route::fallback(function () {
    return response()->error([], 'Not Found', 404);
});