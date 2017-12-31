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
    return view('welcome')->with(['users' => \App\User::all()]);
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
//Route::get('/aisi', 'AisiController@index')->middleware('auth');
//Route::get('/aisi/new', 'AisiController@newList')->middleware('auth');
//Route::get('/aisi/{catalog_id}', 'AisiController@show')->middleware('auth');
//Route::get('/aisi/pic/{suit_id}', 'AisiController@pics')->middleware('auth');
//
//Route::get('comic/','ComicController@index');
//Route::get('comic/download','ComicController@download');

// 原型
Route::get('prototype/upload', 'PrototypeController@uploadPage');
Route::get('prototype', 'PrototypeController@index');
Route::get('prototype/{category}', 'PrototypeController@prototypeList');
Route::post('prototype/upload', 'PrototypeController@upload');