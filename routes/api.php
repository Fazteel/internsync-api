<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\Auth\PasswordController;

use App\Http\Controllers\Api\V1\Admin\UserController;
use App\Http\Controllers\Api\V1\Admin\MajorController;
use App\Http\Controllers\Api\V1\Admin\AcademicYearController;
use App\Http\Controllers\Api\V1\Admin\AdminDashboardController;
use App\Http\Controllers\Api\V1\Admin\ClassroomController;
use App\Http\Controllers\Api\V1\Admin\SettingController;
use App\Http\Controllers\Api\V1\Admin\MasterImportController;

use App\Http\Controllers\Api\V1\Hubin\DepartureController;
use App\Http\Controllers\Api\V1\Hubin\HubinDashboardController;
use App\Http\Controllers\Api\V1\Hubin\IndustryController;
use App\Http\Controllers\Api\V1\Hubin\ReportController;
use App\Http\Controllers\Api\V1\Hubin\VisitApprovalController;

use App\Http\Controllers\Api\V1\Koordinator\KoordinatorDashboardController;
use App\Http\Controllers\Api\V1\Koordinator\PlacementController;
use App\Http\Controllers\Api\V1\Koordinator\SummaryController;
use App\Http\Controllers\Api\V1\Koordinator\SupervisorController;

use App\Http\Controllers\Api\V1\Pembimbing\EvaluationController;
use App\Http\Controllers\Api\V1\Pembimbing\PembimbingDashboardController;
use App\Http\Controllers\Api\V1\Pembimbing\IndustryVisitController;
use App\Http\Controllers\Api\V1\Pembimbing\LogbookApprovalController;
use App\Http\Controllers\Api\V1\Pembimbing\SuperviseeController;

use App\Http\Controllers\Api\V1\Siswa\StudentDashboardController;
use App\Http\Controllers\Api\V1\Siswa\StudentEvaluationController;
use App\Http\Controllers\Api\V1\Siswa\StudentLogbookController;
use App\Http\Controllers\Api\V1\Siswa\StudentPlacementController;
use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/forgot-password', [PasswordController::class, 'sendResetLink']);
    Route::post('/reset-password', [PasswordController::class, 'resetPassword']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/notifications', [NotificationController::class, 'index']);
        Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);

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
            Route::get('dashboard/stats', [HubinDashboardController::class, 'stats']);
            Route::apiResource('industries', IndustryController::class);
            Route::get('departures', [DepartureController::class, 'index']);
            Route::post('departures/{id}/verify', [DepartureController::class, 'verify']);
            Route::get('departures/{id}/print', [DepartureController::class, 'printSurat']);
            Route::post('departures/{id}/generate-surat', [DepartureController::class, 'generateSurat']);
            Route::get('departures/{id}/view-surat', [DepartureController::class, 'viewSurat']);
            Route::get('visit-approvals', [VisitApprovalController::class, 'index']);
            Route::put('visit-approvals/{id}/verify', [VisitApprovalController::class, 'verify']);
            Route::post('visit-approvals/{id}/generate', [VisitApprovalController::class, 'generateSurat']);
            Route::get('visit-approvals/{id}/view', [VisitApprovalController::class, 'viewSurat']);
            Route::get('reports/master', [ReportController::class, 'index']);
            Route::get('reports/export/pdf', [ReportController::class, 'downloadPdf']);
            Route::get('reports/export/excel', [ReportController::class, 'downloadExcel']);
            Route::get('reports/master', [ReportController::class, 'index']);
        });

        Route::prefix('koordinator')->group(function () {
            Route::get('dashboard/stats', [KoordinatorDashboardController::class, 'stats']);
            Route::get('placements', [PlacementController::class, 'index']);
            Route::get('placements/industries', [PlacementController::class, 'industries']);
            Route::post('placements', [PlacementController::class, 'store']);
            Route::get('teachers', [SupervisorController::class, 'teachers']);
            Route::get('supervisor-assignment', [SupervisorController::class, 'index']);
            Route::post('supervisor-assignment', [SupervisorController::class, 'store']);
            Route::get('summary', [SummaryController::class, 'index']);
            Route::get('summary/export/excel', [SummaryController::class, 'downloadExcel']);
            Route::get('summary/export/pdf/{id}', [SummaryController::class, 'downloadStudentPDF']);
        });

        Route::prefix('siswa')->group(function () {
            Route::get('dashboard/stats', [StudentDashboardController::class, 'stats']);
            Route::apiResource('logbooks', StudentLogbookController::class)->only(['index', 'store', 'update']);
            Route::get('my-placement', [StudentPlacementController::class, 'show']);
            Route::get('my-placement/download-letter', [StudentPlacementController::class, 'downloadLetter']);
            Route::get('my-evaluation', [StudentEvaluationController::class, 'index']);
            Route::get('my-evaluation/download', [StudentEvaluationController::class, 'download']);
        });

        Route::prefix('pembimbing')->group(function () {
            Route::get('dashboard', [PembimbingDashboardController::class, 'index']);
            Route::get('students', [SuperviseeController::class, 'index']);
            Route::get('students/{id}', [SuperviseeController::class, 'show']);
            Route::get('logbooks', [LogbookApprovalController::class, 'index']);
            Route::put('logbooks/{id}/verify', [LogbookApprovalController::class, 'verify']);
            Route::put('logbooks/bulk-verify', [LogbookApprovalController::class, 'bulkVerify']);
            Route::get('visits', [IndustryVisitController::class, 'index']);
            Route::post('visits', [IndustryVisitController::class, 'store']);
            Route::get('visits/{id}/view-pdf', [IndustryVisitController::class, 'viewSPPD']);
            Route::get('visits-industries', [IndustryVisitController::class, 'industries']);
            Route::get('evaluations', [EvaluationController::class, 'index']);
            Route::post('evaluations', [EvaluationController::class, 'store']);
        });
    });
});
