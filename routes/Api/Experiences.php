<?php
Route::group(['namespace' => 'Api'], function()
{
    //Route::get('experiences', 'APIExperiencesController@index')->name('experiences.index');
    Route::post('experiences/create', 'APIExperiencesController@create')->name('experiences.create');
    Route::post('experiences/edit', 'APIExperiencesController@edit')->name('experiences.edit');
    Route::post('experiences/show', 'APIExperiencesController@show')->name('experiences.show');
    Route::post('experiences/delete', 'APIExperiencesController@delete')->name('experiences.delete');
});
?>