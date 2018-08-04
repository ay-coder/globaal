<?php
Route::group(['namespace' => 'Api'], function()
{
    Route::get('patient', 'APIPatientController@index')->name('patient.index');
    Route::post('patient/create', 'APIPatientController@create')->name('patient.create');
    Route::post('patient/edit', 'APIPatientController@edit')->name('patient.edit');
    Route::post('patient/show', 'APIPatientController@show')->name('patient.show');
    Route::post('patient/delete', 'APIPatientController@delete')->name('patient.delete');
});
?>