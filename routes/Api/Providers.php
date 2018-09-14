<?php
Route::group(['namespace' => 'Api'], function()
{
    Route::post('providers', 'APIProvidersController@index')->name('providers.index');
    Route::post('providers/create', 'APIProvidersController@create')->name('providers.create');
    Route::post('providers/edit', 'APIProvidersController@edit')->name('providers.edit');
    Route::post('providers/show', 'APIProvidersController@show')->name('providers.show');
    Route::post('providers/delete', 'APIProvidersController@delete')->name('providers.delete');

    Route::post('providers/filter', 'APIProvidersController@filter')->name('providers.filter');

    Route::post('providers/add-service', 'APIProvidersController@addService')->name('providers.add-service');

    Route::post('providers/remove-service', 'APIProvidersController@removeService')->name('providers.remove-service');

    Route::get('providers/company-requests', 'APIProvidersController@companyRequests')->name('providers.company-requests');

    Route::post('providers/accept-company-requests', 'APIProvidersController@acceptCompanyRequests')->name('providers.accept-company-requests');

    Route::post('providers/reject-company-requests', 'APIProvidersController@rejectCompanyRequests')->name('providers.reject-company-requests');

    Route::post('providers/add-company', 'APIProvidersController@addCompany')->name('providers.add-provider');
    
    Route::post('providers/remove-company', 'APIProvidersController@removeCompany')->name('providers.remove-company');

    Route::post('providers/search-company', 'APIProvidersController@searchCompany')->name('companies.search-company');
});
?>
