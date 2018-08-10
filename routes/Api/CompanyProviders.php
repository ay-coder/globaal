<?php
Route::group(['namespace' => 'Api'], function()
{
    Route::get('companyproviders', 'APICompanyProvidersController@index')->name('companyproviders.index');
    Route::post('companyproviders/create', 'APICompanyProvidersController@create')->name('companyproviders.create');
    Route::post('companyproviders/edit', 'APICompanyProvidersController@edit')->name('companyproviders.edit');
    Route::post('companyproviders/show', 'APICompanyProvidersController@show')->name('companyproviders.show');
    Route::post('companyproviders/delete', 'APICompanyProvidersController@delete')->name('companyproviders.delete');
});
?>