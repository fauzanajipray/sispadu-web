<?php

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
    'namespace'  => 'App\Http\Controllers\Api',

], function ($router) {

    // AuthController
    Route::post('login', 'AuthController@login');
    
    Route::group([
        'middleware' => ['auth:api', 'is.active:api']
    ], function() {
        // AuthController
        Route::post('logout', 'AuthController@logout');
        Route::post('me', 'AuthController@me');


        Route::get('profile', 'AuthController@profile');
        Route::post('profile', 'AuthController@editProfile');

        // CategoryController
        Route::get('category-list', 'CategoryController@list');
        Route::post('category-add', 'CategoryController@add');
        Route::post('category-update', 'CategoryController@update');
        Route::delete('category-delete', 'CategoryController@delete');
        // Route::resource('category', CategoryController::class);

        // ProductController
        Route::get('product-list', 'ProductController@list');
        Route::post('product-add', 'ProductController@add');
        Route::post('product-update', 'ProductController@update');
        Route::delete('product-delete', 'ProductController@delete');
        // Route::resource('category', CategoryController::class);
    });
});


