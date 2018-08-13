<?php
Route::group(['namespace' => 'Api'], function()
{
    Route::post('schedules', 'APISchedulesController@index')->name('schedules.index');
    Route::post('schedules/create', 'APISchedulesController@create')->name('schedules.create');
    Route::post('schedules/edit', 'APISchedulesController@edit')->name('schedules.edit');
    Route::post('schedules/show', 'APISchedulesController@show')->name('schedules.show');
    Route::post('schedules/delete', 'APISchedulesController@delete')->name('schedules.delete');
});
?>