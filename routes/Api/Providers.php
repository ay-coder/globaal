<?php
Route::group(['namespace' => 'Api'], function()
{
    Route::post('providers', 'APIProvidersController@index')->name('providers.index');
    Route::post('providers/create', 'APIProvidersController@create')->name('providers.create');
    Route::post('providers/edit', 'APIProvidersController@edit')->name('providers.edit');
    Route::post('providers/show', 'APIProvidersController@show')->name('providers.show');
    Route::post('providers/delete', 'APIProvidersController@delete')->name('providers.delete');

    Route::post('providers/add-service', 'APIProvidersController@addService')->name('providers.add-service');
    Route::post('providers/remove-service', 'APIProvidersController@removeService')->name('providers.remove-service');
});
?>
