<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\Admin\UserController;
use App\Http\Controllers\Api\V1\Admin\MajorController;
use App\Http\Controllers\Api\V1\Admin\AcademicYearController;
use App\Http\Controllers\Api\V1\Admin\AdminDashboardController;
use App\Http\Controllers\Api\V1\Admin\ClassroomController;
use App\Http\Controllers\Api\V1\Admin\MasterImportController;
use App\Http\Controllers\Api\V1\Admin\SettingController;
use App\Http\Controllers\Api\V1\Auth\PasswordController;
use App\Http\Controllers\Api\V1\Hubin\DepartureController;
use App\Http\Controllers\Api\V1\Hubin\IndustryController;
use App\Http\Controllers\Api\V1\Koordinator\KoordinatorDashboardController;
use App\Http\Controllers\Api\V1\Koordinator\PlacementController;
use App\Http\Controllers\Api\V1\Koordinator\SupervisorController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::post('/login', [AuthController::class, 'login']); 
    Route::post('/forgot-password', [PasswordController::class, 'sendResetLink']);
    Route::post('/reset-password', [PasswordController::class, 'resetPassword']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/logout', [AuthController::class, 'logout']);
        
        Route::prefix('admin')->group(function () {
            Route::get('dashboard/stats', [AdminDashboardController::class, 'stats']);
            Route::get('logs', [AdminDashboardController::class, 'logs']);
            Route::post('users/import', [UserController::class, 'import']);
            Route::post('masters/import', [MasterImportController::class, 'import']);
            Route::apiResource('users', UserController::class);
            Route::post('users/{user}/reset-password', [UserController::class, 'resetPassword']);
            Route::apiResource('majors', MajorController::class);
            Route::apiResource('academic-years', AcademicYearController::class);
            Route::apiResource('classrooms', ClassroomController::class);
            Route::get('settings', [SettingController::class, 'index']);
            Route::post('settings', [SettingController::class, 'update']);
            Route::post('users/{user}/resend-activation', [UserController::class, 'resendActivationEmail']);
        });

        Route::prefix('hubin')->group(function () {
            Route::apiResource('industries', IndustryController::class);
            Route::get('departures', [DepartureController::class, 'index']);
            Route::post('departures/{id}/verify', [DepartureController::class, 'verify']);
            Route::get('departures/{id}/print', [DepartureController::class, 'printSurat']);
        });

        Route::prefix('koordinator')->group(function () {
            Route::get('dashboard/stats', [KoordinatorDashboardController::class, 'stats']);
            Route::get('placements', [PlacementController::class, 'index']);
            Route::get('placements/industries', [PlacementController::class, 'industries']);
            Route::post('placements', [PlacementController::class, 'store']);
            Route::get('teachers', [SupervisorController::class, 'teachers']);
            Route::get('plotting-pembimbing', [SupervisorController::class, 'index']);
            Route::post('plotting-pembimbing', [SupervisorController::class, 'store']);
        });
    });
});