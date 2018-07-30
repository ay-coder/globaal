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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['namespace' => 'Api',], function () 
{
    Route::post('login', 'UsersController@login')->name('api.login');
    Route::post('register', 'UsersController@create')->name('api.register');
    Route::post('register_company', 'UsersController@createCompany')->name('api.register-company');
    Route::post('register_provider', 'UsersController@createProvider')->name('api.register-provider');
    Route::post('social-register', 'UsersController@socialCreate')->name('api.social-register');
    Route::post('social-login', 'UsersController@socialLogin')->name('api.login');
    Route::post('forgotpassword', 'UsersController@forgotPassword')->name('api.forgotPassword');
    Route::get('config', 'UsersController@config')->name('api.config');
});

Route::group(['middleware' => 'jwt.customauth'], function () 
{
    includeRouteFiles(__DIR__.'/Api/');
});