<?php

Route::group([
    "namespace"  => "Credentials",
], function () {
    /*
     * Admin Credentials Controller
     */

    // Route for Ajax DataTable
    Route::get("credentials/get", "AdminCredentialsController@getTableData")->name("credentials.get-list-data");

    Route::resource("credentials", "AdminCredentialsController");
});