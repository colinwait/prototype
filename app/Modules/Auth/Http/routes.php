<?php

Route::group(['middleware' => 'web', 'prefix' => 'auth', 'namespace' => 'App\\Modules\Auth\Http\Controllers'], function()
{
    Route::get('/', 'AuthController@index');
});
Route::get('aisi', 'ImageController@index');