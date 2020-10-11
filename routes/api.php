<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::prefix('auth')->group(function () {
    Route::post('register', 'AuthController@register');
    Route::post('login', 'AuthController@login');
    Route::post('refresh-token', 'AuthController@refresh');
    Route::get('me', 'AuthController@me');
});

Route::middleware('auth.api')->group(function () {

    Route::get('process', 'ProcessController@index');

    Route::get('directories', 'DirectoryController@index');
    Route::post('directories', 'DirectoryController@make');
    
    Route::get('files', 'FileController@index');
    Route::post('files', 'FileController@make');
});