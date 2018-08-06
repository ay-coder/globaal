<?php

Route::group([
    "namespace"  => "ProviderServices",
], function () {
    /*
     * Admin ProviderServices Controller
     */

    // Route for Ajax DataTable
    Route::get("providerservices/get", "AdminProviderServicesController@getTableData")->name("providerservices.get-list-data");

    Route::resource("providerservices", "AdminProviderServicesController");
});