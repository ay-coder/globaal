<?php
Route::group(['namespace' => 'Api'], function()
{
    Route::get('credentials', 'APICredentialsController@index')->name('credentials.index');
    Route::post('credentials/create', 'APICredentialsController@create')->name('credentials.create');
    Route::post('credentials/edit', 'APICredentialsController@edit')->name('credentials.edit');
    Route::post('credentials/show', 'APICredentialsController@show')->name('credentials.show');
    Route::post('credentials/delete', 'APICredentialsController@delete')->name('credentials.delete');
});
?>