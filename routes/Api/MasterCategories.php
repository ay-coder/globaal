<?php
Route::group(['namespace' => 'Api'], function()
{
    
    Route::post('mastercategories/create', 'APIMasterCategoriesController@create')->name('mastercategories.create');
    Route::post('mastercategories/edit', 'APIMasterCategoriesController@edit')->name('mastercategories.edit');
    Route::post('mastercategories/show', 'APIMasterCategoriesController@show')->name('mastercategories.show');
    Route::post('mastercategories/delete', 'APIMasterCategoriesController@delete')->name('mastercategories.delete');
});
?>