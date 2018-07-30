<?php

Route::group([
    "namespace"  => "Companies",
], function () {
    /*
     * Admin Companies Controller
     */

    // Route for Ajax DataTable
    Route::get("companies/get", "AdminCompaniesController@getTableData")->name("companies.get-list-data");

    Route::resource("companies", "AdminCompaniesController");
});