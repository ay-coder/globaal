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

    Route::get('experiences', 'APIExperiencesController@index')->name('api.experiences');

    Route::get('master-categories', 'APIMasterCategoriesController@index')->name('mastercategories.index');

    Route::get('companies', 'APICompaniesController@index')->name('companies.index');
});

Route::group(['namespace' => 'Api', 'middleware' => 'jwt.customauth'], function () 
{
    Route::post('change_password', 'UsersController@changePassword')->name('api.change-password');
    Route::post('update-password', 'UsersController@updageUserPassword')->name('api.update-user-password');
    Route::get('logout', 'UsersController@logout')->name('api.logout');
});
Route::group(['middleware' => 'jwt.customauth'], function () 
{
    includeRouteFiles(__DIR__.'/Api/');
});