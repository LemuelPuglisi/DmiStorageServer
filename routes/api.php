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

    Route::resource('courses', 'CoursesController', ["except" => ['edit', 'create']]); 
    Route::get('courses/sort/{param}/{order}', 'CoursesController@orderedIndex')->name('courses.sort'); 

    // Route::middleware('auth:api')->get('/user', function (Request $request) {
    //     return $request->user();
    // });