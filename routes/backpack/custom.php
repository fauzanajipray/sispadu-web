<?php

use App\Http\Controllers\Admin\UserCrudController;
use Illuminate\Support\Facades\Route;

// --------------------------
// Custom Backpack Routes
// --------------------------
// This route file is loaded automatically by Backpack\Base.
// Routes you generate using Backpack\Generators will be placed here.

Route::group([
    // 'prefix'     => config('backpack.base.route_prefix', 'admin'),
    'middleware' => array_merge(
        (array) config('backpack.base.web_middleware', 'web'),
        (array) config('backpack.base.middleware_key', 'admin')
    ),
    'namespace'  => 'App\Http\Controllers\Admin',
], function () { // custom admin routes
    Route::crud('user', 'UserCrudController');
    // Route::get('user/update-position', [UserCrudController::class, 'updatePosition']);
    Route::post('user/update-position', [UserCrudController::class, 'updatePosition'])->name('user.update-position');
    Route::crud('outlet', 'OutletCrudController');
    Route::crud('position', 'PositionCrudController');
    Route::crud('report', 'ReportCrudController');
    
    
    Route::get('show-hierarchy', 'PositionCrudController@showHierarchy')
        ->name('position.show-hierarchy');

    // API
    Route::prefix('webapi')->name('webapi.')->group(function (){
        Route::prefix('user')->name('user.')->group(function (){
            Route::get('{id}', 'UserCrudController@getData')->name('get-data');
        });
        Route::prefix('position')->name('position.')->group(function (){
            Route::post('list-parent', 'PositionCrudController@listParentPositions')->name('list-parent');
            Route::get('list-without-user/{id}', 'PositionCrudController@listPositionsWithoutUser')->name('list-without-user');
        });
    });

}); // this should be the absolute last line of this file