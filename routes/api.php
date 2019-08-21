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


    /*
    *   Courses Routing  
    */
    Route::resource('courses', 'CoursesController', ["except" => ['edit', 'create']]); 
    Route::get('courses/sort/{param}/{order}', 'CoursesController@orderedIndex')->name('courses.sort'); 
    Route::get('courses/{id}/folders', 'CoursesController@folders')->name('courses.folders'); 
    Route::get('courses/{id}/folders/sort/{param}/{order}', 'CoursesController@orderedFolders')->name('courses.folders.sort'); 

    /*
    *   Folder Routing  
    */
    Route::resource('folders', 'FoldersController', ["except" => ['edit', 'create']]); 
    Route::get('folders/{id}/course', 'FoldersController@course')->name('folders.course');
