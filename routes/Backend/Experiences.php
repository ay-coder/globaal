<?php

Route::group([
    "namespace"  => "Experiences",
], function () {
    /*
     * Admin Experiences Controller
     */

    // Route for Ajax DataTable
    Route::get("experiences/get", "AdminExperiencesController@getTableData")->name("experiences.get-list-data");

    Route::resource("experiences", "AdminExperiencesController");
});