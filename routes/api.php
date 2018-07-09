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
Route::group(['prefix' => 'v1'], function() {
    // Authentication protected routes
    Route::middleware('auth:api')->group(function () {
        // Auth routes
        Route::group(['prefix' => 'auth'], function() {
            Route::get('/', 'AuthController@getAuthenticatedUser');
        });
        // Tutor routes
        Route::group(['prefix' => 'tutors', 'middleware' => 'self'], function() {
            Route::prefix('/{tutor}/students')->group(function() {
                Route::get('/', 'TutorController@getStudents');
                Route::post('/', 'TutorController@createStudent');
                // Make the below restricted to only the student's tutor
                Route::get('{student}', 'TutorController@getStudent');
                Route::delete('{student}', 'TutorController@deleteStudent');
            });
            Route::prefix('/{tutor}/lessons')->group(function() {
                Route::post('/', 'TutorController@createLesson');
            });
        });
        // Lesson routes
        // TODO: set an admin middleware, then tutors can access through a redirect within their middleware
        Route::group(['prefix' => 'lessons'], function() {
            Route::get('/', 'LessonController@list');
            Route::get('/{lesson}', 'LessonController@get');
        });
    });

    // test
    Route::get('/', function() {
        return response()->json(['someData' => 1, 200]);
    });

    Route::prefix('auth')->group(function() {
        Route::post('/', 'AuthController@create');
    });

    // TODO: move this inside auth group (have initial user set up with a seeder)
    Route::prefix('users')->group(function() {
        Route::post('/', 'UserController@create'); // IP
        Route::patch('/{user}', 'UserController@update');
    });
});
