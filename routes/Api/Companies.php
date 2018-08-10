<?php
Route::group(['namespace' => 'Api'], function()
{
    
    Route::get('all_companies', 'APICompaniesController@getAll')->name('companies.all-companies');

    Route::post('companies/create', 'APICompaniesController@create')->name('companies.create');
    Route::post('companies/edit', 'APICompaniesController@edit')->name('companies.edit');
    Route::post('companies/show', 'APICompaniesController@show')->name('companies.show');
    Route::post('companies/delete', 'APICompaniesController@delete')->name('companies.delete');

    Route::post('get_companies_providers', 'APICompaniesController@getAllProviders')->name('companies.get-all-providers');

    /*Route::post('companies/add-provider', 'APICompaniesController@addProvider')->name('companies.add-provider');
    Route::post('companies/remove-provider', 'APICompaniesController@removeProvider')->name('companies.remove-provider');*/
});
?>
