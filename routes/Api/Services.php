<?php
Route::group(['namespace' => 'Api'], function()
{
    Route::get('services', 'APIServicesController@index')->name('services.index');
    Route::post('services/create', 'APIServicesController@create')->name('services.create');
    Route::post('services/edit', 'APIServicesController@edit')->name('services.edit');
    Route::post('services/show', 'APIServicesController@show')->name('services.show');
    Route::post('services/delete', 'APIServicesController@delete')->name('services.delete');
});
?>