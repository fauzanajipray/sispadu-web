<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ReportController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::group([
    'middleware' => ['api', 'assign.guard:api'],
    // The 'namespace' key is deprecated in newer Laravel versions.
    // It's better to use the modern array syntax with full class imports.
], function () {
    // --- Public Routes ---
    Route::post('login', [AuthController::class, 'login']);
    Route::get('report', [ReportController::class, 'index']);

    Route::group([
        'middleware' => ['auth:api']
    ], function () {
        // --- Authenticated Routes ---

        // Auth
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('me', [AuthController::class, 'me']);
        Route::get('profile', [AuthController::class, 'profile']);
        Route::post('profile', [AuthController::class, 'editProfile']);

        // Reports
        // The {report} parameter name now matches the $report variable in the controller,
        // enabling automatic Route Model Binding.
        Route::get('report/my-reports', [ReportController::class, 'myReports']);
        Route::post('report', [ReportController::class, 'store']);
        Route::post('report/{report}/cancel', [ReportController::class, 'cancelReport']);
        Route::post('report/{report}/action', [ReportController::class, 'processReportAction']);
        Route::get('report/{report}', [ReportController::class, 'show']);
    });
});
