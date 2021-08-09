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

Route::group(['prefix' => 'auth'], function () {
    Route::post('signup', 'Auth\AuthController@signup');
    Route::post('login', 'Auth\AuthController@login');
});

Route::group(['prefix' => 'v1'], function () {
    Route::resource('plans', 'PlanController', ['only' => ['index', 'show', 'store', 'destroy']]);
});

Route::group(['middleware' => ['auth:jwt'], 'prefix' => 'v1'], function () {
    Route::resource('orders', 'OrderController', ['only' => ['index', 'show', 'store', 'destroy']]);
});
