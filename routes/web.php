<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});



Route::group(['middleware' => ['web']], function () {
    Route::get('upload', function () {
        return view('files.upload');
    });

    Route::post('/handleUpload', 'FilesController@handleUpload');
});

Route::resource('posts', 'PostsController');
Route::resource('posts', 'PostsController');
Route::resource('posts', 'PostsController');
Route::resource('admin/posts', 'Admin\\PostsController');
Route::resource('admin/posts', 'Admin\\PostsController');
Route::resource('posts', 'PostsController');
Route::resource('posts', 'PostsController');
Route::resource('posts', 'PostsController');
