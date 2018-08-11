<?php
Route::group(['namespace' => 'Api'], function()
{
    
    Route::get('all_companies', 'APICompaniesController@getAll')->name('companies.all-companies');

    Route::post('companies/create', 'APICompaniesController@create')->name('companies.create');
    Route::post('companies/edit', 'APICompaniesController@edit')->name('companies.edit');
    Route::post('companies/show', 'APICompaniesController@show')->name('companies.show');
    Route::post('companies/delete', 'APICompaniesController@delete')->name('companies.delete');

    Route::post('get_companies_providers', 'APICompaniesController@getAllProviders')->name('companies.get-all-providers');

    Route::get('companies/provider-requests', 'APICompaniesController@providerRequests')->name('companies.provider-requests');

    Route::post('companies/accept-provider-requests', 'APICompaniesController@acceptProviderRequests')->name('companies.accept-provider-requests');

    Route::post('companies/reject-company-requests', 'APICompaniesController@rejectProviderRequests')->name('companies.reject-provider-requests');

    /*Route::post('companies/add-provider', 'APICompaniesController@addProvider')->name('companies.add-provider');
    Route::post('companies/remove-provider', 'APICompaniesController@removeProvider')->name('companies.remove-provider');*/
});
?>
