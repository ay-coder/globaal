<?php
Route::group(['namespace' => 'Api'], function()
{
    Route::get('testimonials', 'APITestimonialsController@index')->name('testimonials.index');
Route::post('testimonials/create', 'APITestimonialsController@edit@create')->name('testimonials.create');
    Route::post('testimonials/edit', 'APITestimonialsController@edit')->name('testimonials.edit');
    Route::post('testimonials/show', 'APITestimonialsController@show')->name('testimonials.show');
    Route::post('testimonials/delete', 'APITestimonialsController@delete')->name('testimonials.delete');

     Route::post('testimonials/company', 'APITestimonialsController@getByCompanies')->name('testimonials.index');

     Route::post('testimonials/provider', 'APITestimonialsController@getByProvider')->name('testimonials.index');
});
?>