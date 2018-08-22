<?php
Route::group(['namespace' => 'Api'], function()
{
    Route::get('companyservices', 'APICompanyServicesController@index')->name('companyservices.index');
    Route::post('companyservices/create', 'APICompanyServicesController@create')->name('companyservices.create');
    Route::post('companyservices/edit', 'APICompanyServicesController@edit')->name('companyservices.edit');
    Route::post('companyservices/show', 'APICompanyServicesController@show')->name('companyservices.show');
    Route::post('companyservices/delete', 'APICompanyServicesController@delete')->name('companyservices.delete');
});
?>