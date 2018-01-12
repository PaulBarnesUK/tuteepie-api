<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::group(['prefix' => 'v1', 'middleware' => 'cors'], function() {
    // Authentication protected routes
    Route::middleware('auth:api')->group(function () {
        Route::get('admin', function() {
            return response()->json([
                'data' => auth()->user()->name
            ], 200);
        });
        Route::group(['prefix' => 'tutors', 'middleware' => 'self'], function() {
            Route::prefix('/{tutor}/students')->group(function() {
                Route::post('/', 'TutorController@createStudent');
                Route::get('{student}', 'TutorController@getStudent');
                Route::delete('{student}', 'TutorController@deleteStudent');
            });
            Route::prefix('/{tutor}/lessons')->group(function() {
                Route::post('/', 'TutorController@createLesson');
            });
        });
    });

    Route::get('/', function() {
        return response()->json(['someData' => 1, 200]);
    });

    Route::prefix('auth')->group(function() {
        Route::post('/', 'AuthController@create');
    });

    Route::prefix('users')->group(function() {
        Route::post('/', 'UserController@create'); // IP
        Route::patch('/{user}', 'UserController@update');
    });
});
