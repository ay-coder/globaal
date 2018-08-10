<?php

Route::group([
    "namespace"  => "Testimonials",
], function () {
    /*
     * Admin Testimonials Controller
     */

    // Route for Ajax DataTable
    Route::get("testimonials/get", "AdminTestimonialsController@getTableData")->name("testimonials.get-list-data");

    Route::resource("testimonials", "AdminTestimonialsController");
});