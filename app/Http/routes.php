<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    return view('welcome');
});

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Test route
Route::get('/test', function () {
    return response()->json(['message' => 'API is working', 'version' => '1.0']);
});

// Protected routes
Route::group(['middleware' => 'auth.token'], function () {
    Route::get('/tasks', 'TaskController@index');
    Route::post('/tasks', 'TaskController@store');
    Route::get('/tasks/{id}', 'TaskController@show');
    Route::put('/tasks/{id}', 'TaskController@update');
    Route::delete('/tasks/{id}', 'TaskController@destroy');
});
