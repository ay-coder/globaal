<?php

Route::group([
    "namespace"  => "Providers",
], function () {
    /*
     * Admin Providers Controller
     */

    // Route for Ajax DataTable
    Route::get("providers/get", "AdminProvidersController@getTableData")->name("providers.get-list-data");

    Route::resource("providers", "AdminProvidersController");
});