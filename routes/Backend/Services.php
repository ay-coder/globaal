<?php

Route::group([
    "namespace"  => "Services",
], function () {
    /*
     * Admin Services Controller
     */

    // Route for Ajax DataTable
    Route::get("services/get", "AdminServicesController@getTableData")->name("services.get-list-data");

    Route::resource("services", "AdminServicesController");
});