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
| **Commented APIs has to be implemented.
*/

    /**
     *  Authenticated routes
     */
    Route::middleware('auth:api')->group(function () {

        /**
         *  Authenticated user routes
         */
        Route::get('users', 'UserController@index')->name('users.index'); 
        Route::get('users/admins', 'UserController@indexAdmins')->name('users.index.admins'); 
        Route::get('user', 'UserController@details')->name('user.details'); 
        Route::delete('users/{id}', 'UserController@destroy')->name('user.delete');  
        Route::put('users/{id}', 'UserController@update')->name('user.update'); 
        Route::put('users/{id}/role', 'UserController@changeRole')->name('user.update.role');
        Route::get('users/{id}/portability', 'UserController@portability')->name('user.portability'); 
        Route::get('user/{id}/courses/requests', 'UserController@courseRequests')->name('user.course.requests');
        Route::get('user/{id}/folders/requests', 'UserController@folderRequests')->name('user.folder.requests');        

        /**
         *  Authenticated courses routes
         */
        Route::resource('courses', 'CourseController')->only(['store', 'update', 'destroy']);
        Route::get('courses/{id}/requests', 'CourseController@requests')->name('courses.requests'); 

        /**
         *  Authenticated folders routes
         */
        Route::resource('folders', 'FolderController')->only(['store', 'update', 'destroy']);
        Route::get('folders/{id}/requests', 'FolderController@requests')->name('folders.requests');
        
        /**
         *  Authenticated files routes
         */
        Route::resource('files', 'FileController')->only(['store', 'update', 'destroy']);

        /**
         *  Throttle protection to permission requests
         */
        Route::middleware('throttle:300,5')->group(function () {
            /**
             *  Authenticated course requests routes
             */
            Route::resource('courses/requests', 'CourseRequestController')->except(['create', 'edit', 'update']); 
            Route::put('courses/requests/{id}/manage', 'CourseRequestController@manage')->name('courses.request.manage'); 
        
            /**
             *  Authenticated folder requests routes
             */
            Route::resource('folders/requests', 'FolderRequestController')->except(['create', 'edit', 'update']); 
            Route::put('folders/requests/{id}/manage', 'FolderRequestController@manage')->name('folder.request.manage');
            Route::put('folders/requests/{id}/upgrade', 'FolderRequestController@upgrade')->name('folder.request.upgrade'); 
        });

    });

    /**
     * User Routing (Throttle limit)
    */
    Route::middleware('throttle:10,10')->group(function () {

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
    Route::get('courses/{id}/user', 'CourseController@user')->name('courses.user'); 

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
    Route::get('folders/{id}/user', 'FolderController@user')->name('folders.user'); 

    /*
    *   Files Routing  
    */
    Route::resource('files', 'FileController')->only(['index', 'show']);
    Route::get('files/{ext}/ext', 'FileController@getFilesByExt')->name('files.extension'); 
    Route::get('files/{id}/download', 'FileController@downloadFile')->name('files.download');
    Route::get('files/{id}/stream', 'FileController@streamFile')->name('files.stream'); 
    Route::get('files/{id}/folder', 'FileController@folder')->name('files.folder'); 
    Route::get('files/{id}/user', 'FileController@user')->name('files.user'); 
