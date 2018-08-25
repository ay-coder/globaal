<?php
Route::group(['namespace' => 'Api'], function()
{
    Route::post('provider-services', 'APIProviderServicesController@index')->name('providerservices.index');

    Route::post('provider-services-search', 'APIProviderServicesController@search')->name('providerservices.search');


    Route::post('provider-services/add', 'APIProviderServicesController@create')->name('providerservices.create');


    Route::post('providerservices/edit', 'APIProviderServicesController@edit')->name('providerservices.edit');
    Route::post('providerservices/show', 'APIProviderServicesController@show')->name('providerservices.show');


    Route::post('provider-services/remove', 'APIProviderServicesController@delete')->name('providerservices.delete');
});
?>