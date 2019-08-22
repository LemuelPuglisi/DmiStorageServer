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
    Route::get('courses/{id}/trend/{limit}', 'CoursesController@getMostViewedFiles')->name('courses.trend'); 

    /*
    *   Folders Routing  
    */
    Route::resource('folders', 'FoldersController', ["except" => ['edit', 'create']]); 
    Route::get('folders/{id}/course', 'FoldersController@course')->name('folders.course');
    Route::get('folders/{id}/parent', 'FoldersController@parent')->name('folders.parent');
    Route::get('folders/{id}/files', 'FoldersController@files')->name('folders.files');  
    Route::get('folders/{id}/subfolders', 'FoldersController@subfolders')->name('folders.subfolders'); 
    Route::get('folders/{id}/files/sort/{param}/{order}', 'FoldersController@orderedFiles')->name('folders.files.sort'); 
    Route::get('folders/{id}/files/{ext}/ext', 'FoldersController@getFileByExt')->name('folders.files.extension');

    /*
    *   Files Routing  
    */
    Route::resource('files', 'FilesController', ["except" => ['edit', 'create']]); 
    Route::get('files/{ext}/ext', 'FilesController@getFilesByExt')->name('files.extension'); 
    Route::get('files/{id}/download', 'FilesController@downloadFile')->name('files.download');
    Route::get('files/{id}/stream', 'FilesController@streamFile')->name('files.stream'); 
    Route::get('files/{id}/folder', 'FilesController@folder')->name('files.folder'); 
