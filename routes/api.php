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
Route::post('login','UserController@login')->name('login');
Route::middleware('auth:api')->group(function () {
Route::post('create/code','UserController@generate');
Route::post('list/codes','UserController@index');
Route::post('view/code/{id}','UserController@view');
});
