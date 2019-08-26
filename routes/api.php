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

    /**
     *  Authenticated routes
     */
    Route::middleware('auth:api')->group(function () {

        /**
         *  Authenticated user routes
         */
        Route::get('user', 'UsersController@details')->name('user.details'); 

        /**
         *  Authenticated courses routes
         */
        Route::resource('courses', 'CoursesController')->only(['store', 'update', 'destroy']);

        /**
         *  Authenticated folders routes
         */
        Route::resource('folders', 'FoldersController')->only(['store', 'update', 'destroy']);

        /**
         *  Authenticated files routes
         */
        Route::resource('files', 'FilesController')->only(['store', 'update', 'destroy']);

    });

    /**
     * User Routing (Throttle limit)
    */
    Route::middleware('throttle:5,10')->group(function () {

        Route::post('auth/signup', 'UsersController@register')->name('user.signup');
        Route::post('auth/forgot', 'ForgotPasswordController@sendResetLinkEmail')->name('user.forgot');
        Route::post('auth/reset', 'ResetPasswordController@reset')->name('user.reset'); 

    });

    Route::get('users/{id}/files', 'UsersController@uploadedFiles')->name('user.files'); 


    /*
    *   Courses Routing  
    */
    Route::resource('courses', 'CoursesController')->only(['index', 'show']); 
    Route::get('courses/sort/{param}/{order}', 'CoursesController@orderedIndex')->name('courses.sort'); 
    Route::get('courses/{id}/folders', 'CoursesController@folders')->name('courses.folders'); 
    Route::get('courses/{id}/folders/sort/{param}/{order}', 'CoursesController@orderedFolders')->name('courses.folders.sort'); 
    Route::get('courses/{id}/trend/{limit}', 'CoursesController@getMostViewedFiles')->name('courses.trend'); 

    /*
    *   Folders Routing  
    */
    Route::resource('folders', 'FoldersController')->only(['index', 'show']);  
    Route::get('folders/{id}/course', 'FoldersController@course')->name('folders.course');
    Route::get('folders/{id}/parent', 'FoldersController@parent')->name('folders.parent');
    Route::get('folders/{id}/files', 'FoldersController@files')->name('folders.files');  
    Route::get('folders/{id}/subfolders', 'FoldersController@subfolders')->name('folders.subfolders'); 
    Route::get('folders/{id}/files/sort/{param}/{order}', 'FoldersController@orderedFiles')->name('folders.files.sort'); 
    Route::get('folders/{id}/files/{ext}/ext', 'FoldersController@getFileByExt')->name('folders.files.extension');

    /*
    *   Files Routing  
    */
    Route::resource('files', 'FilesController')->only(['index', 'show']);
    Route::get('files/{ext}/ext', 'FilesController@getFilesByExt')->name('files.extension'); 
    Route::get('files/{id}/download', 'FilesController@downloadFile')->name('files.download');
    Route::get('files/{id}/stream', 'FilesController@streamFile')->name('files.stream'); 
    Route::get('files/{id}/folder', 'FilesController@folder')->name('files.folder'); 
    Route::get('files/{id}/user', 'FilesController@user')->name('files.user'); 