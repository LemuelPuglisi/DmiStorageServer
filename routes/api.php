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
        Route::get('user', 'UserController@details')->name('user.details'); 

        /**
         *  Authenticated courses routes
         */
        Route::resource('courses', 'CourseController')->only(['store', 'update', 'destroy']);

        /**
         *  Authenticated folders routes
         */
        Route::resource('folders', 'FolderController')->only(['store', 'update', 'destroy']);

        /**
         *  Authenticated files routes
         */
        Route::resource('files', 'FileController')->only(['store', 'update', 'destroy']);

    });

    /**
     * User Routing (Throttle limit)
    */
    Route::middleware('throttle:5,10')->group(function () {

        Route::post('auth/signup', 'UserController@register')->name('user.signup');
        Route::post('auth/forgot', 'ForgotPasswordController@sendResetLinkEmail')->name('user.forgot');
        Route::post('auth/reset', 'ResetPasswordController@reset')->name('user.reset'); 

    });

    Route::get('users/{id}/files', 'UserController@uploadedFiles')->name('user.files'); 


    /*
    *   Courses Routing  
    */
    Route::resource('courses', 'CourseController')->only(['index', 'show']); 
    Route::get('courses/sort/{param}/{order}', 'CourseController@orderedIndex')->name('courses.sort'); 
    Route::get('courses/{id}/folders', 'CourseController@folders')->name('courses.folders'); 
    Route::get('courses/{id}/folders/sort/{param}/{order}', 'CourseController@orderedFolders')->name('courses.folders.sort'); 
    Route::get('courses/{id}/trend/{limit}', 'CourseController@getMostViewedFiles')->name('courses.trend'); 

    /*
    *   Folders Routing  
    */
    Route::resource('folders', 'FolderController')->only(['index', 'show']);  
    Route::get('folders/{id}/course', 'FolderController@course')->name('folders.course');
    Route::get('folders/{id}/parent', 'FolderController@parent')->name('folders.parent');
    Route::get('folders/{id}/files', 'FolderController@files')->name('folders.files');  
    Route::get('folders/{id}/subfolders', 'FolderController@subfolders')->name('folders.subfolders'); 
    Route::get('folders/{id}/files/sort/{param}/{order}', 'FolderController@orderedFiles')->name('folders.files.sort'); 
    Route::get('folders/{id}/files/{ext}/ext', 'FolderController@getFileByExt')->name('folders.files.extension');

    /*
    *   Files Routing  
    */
    Route::resource('files', 'FileController')->only(['index', 'show']);
    Route::get('files/{ext}/ext', 'FileController@getFilesByExt')->name('files.extension'); 
    Route::get('files/{id}/download', 'FileController@downloadFile')->name('files.download');
    Route::get('files/{id}/stream', 'FileController@streamFile')->name('files.stream'); 
    Route::get('files/{id}/folder', 'FileController@folder')->name('files.folder'); 
    Route::get('files/{id}/user', 'FileController@user')->name('files.user'); 



