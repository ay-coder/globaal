<?php
Route::group(['namespace' => 'Api'], function()
{
    Route::get('providertypes', 'APIProviderTypesController@index')->name('providertypes.index');
    Route::post('providertypes/create', 'APIProviderTypesController@create')->name('providertypes.create');
    Route::post('providertypes/edit', 'APIProviderTypesController@edit')->name('providertypes.edit');
    Route::post('providertypes/show', 'APIProviderTypesController@show')->name('providertypes.show');
    Route::post('providertypes/delete', 'APIProviderTypesController@delete')->name('providertypes.delete');
});
?>