<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CommentController;
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
    Route::post('register', [AuthController::class, 'register']);
    Route::get('report', [ReportController::class, 'index']);
    Route::get('p/report/{report}', [ReportController::class, 'show']);

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
        
        Route::post('report/image', [ReportController::class, 'uploadImage']);
        Route::get('report/my-reports', [ReportController::class, 'myReports']);
        Route::get('report/position-report', [ReportController::class, 'positionReports']);
        Route::post('report', [ReportController::class, 'store']);
        Route::delete('report/{report}', [ReportController::class, 'cancelReport']);
        Route::post('report/{report}/action', [ReportController::class, 'processReportAction']);
        Route::get('report/{report}', [ReportController::class, 'show']);
        Route::post('report/{report}', [ReportController::class, 'update']);

        Route::get('reports/{report}/comments', [CommentController::class, 'index']);
        Route::post('reports/{report}/comments', [CommentController::class, 'store']);
        Route::put('reports/{report}/comments/{comment}', [CommentController::class, 'update']);
        Route::delete('reports/{report}/comments/{comment}', [CommentController::class, 'destroy']);


        Route::get('positions', [ReportController::class, 'positionList']);
        Route::get('positions-without-me', [ReportController::class, 'positionListWithoutMe']);
    });
});
