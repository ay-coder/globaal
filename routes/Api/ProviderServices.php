<?php
Route::group(['namespace' => 'Api'], function()
{
    Route::get('providerservices', 'APIProviderServicesController@index')->name('providerservices.index');
    Route::post('providerservices/create', 'APIProviderServicesController@create')->name('providerservices.create');
    Route::post('providerservices/edit', 'APIProviderServicesController@edit')->name('providerservices.edit');
    Route::post('providerservices/show', 'APIProviderServicesController@show')->name('providerservices.show');
    Route::post('providerservices/delete', 'APIProviderServicesController@delete')->name('providerservices.delete');
});
?>