<?php

Route::group([
    "namespace"  => "Schedules",
], function () {
    /*
     * Admin Schedules Controller
     */

    // Route for Ajax DataTable
    Route::get("schedules/get", "AdminSchedulesController@getTableData")->name("schedules.get-list-data");

    Route::resource("schedules", "AdminSchedulesController");
});