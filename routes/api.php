<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Test route (should work without authentication)
Route::get('/test', function () {
    return response()->json(['message' => 'API is working', 'version' => '1.0']);
});

// Protected routes that require auth.token middleware
Route::group(['middleware' => 'auth.token'], function () {
    Route::get('/tasks', 'TaskController@index');
    Route::post('/tasks', 'TaskController@store');
    Route::get('/tasks/{id}', 'TaskController@show');
    Route::put('/tasks/{id}', 'TaskController@update');
    Route::delete('/tasks/{id}', 'TaskController@destroy');
    
    // Task categorization routes
    Route::post('/categorize-task', 'TaskCategorizerController@categorizeTask');
    Route::get('/categorize-test', 'TaskCategorizerController@testCategorization');
    Route::get('/categories', 'TaskCategorizerController@getCategories');
});