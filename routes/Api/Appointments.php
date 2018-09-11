<?php
Route::group(['namespace' => 'Api'], function()
{
    Route::post('appointments', 'APIAppointmentsController@index')->name('appointments.index');

    Route::post('appointments/past', 'APIAppointmentsController@getPastData')->name('appointments.past');

    Route::post('appointments/create', 'APIAppointmentsController@create')->name('appointments.create');

    Route::post('appointments/cancel', 'APIAppointmentsController@cancel')->name('appointments.cancel');

    Route::post('appointments/edit', 'APIAppointmentsController@edit')->name('appointments.edit');
    Route::post('appointments/show', 'APIAppointmentsController@show')->name('appointments.show');
    Route::post('appointments/delete', 'APIAppointmentsController@delete')->name('appointments.delete');
});
?>