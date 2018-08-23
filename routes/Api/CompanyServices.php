<?php
Route::group(['namespace' => 'Api'], function()
{
    Route::post('company-services', 'APICompanyServicesController@index')->name('companyservices.index');

    Route::post('company-services-search', 'APICompanyServicesController@search')->name('companyservices.search');
    Route::post('company-services/add', 'APICompanyServicesController@create')->name('companyservices.add');
    Route::post('companyservices/edit', 'APICompanyServicesController@edit')->name('companyservices.edit');
    Route::post('companyservices/show', 'APICompanyServicesController@show')->name('companyservices.show');
    Route::post('company-services/remove', 'APICompanyServicesController@remove')->name('companyservices.remove');
});
?>