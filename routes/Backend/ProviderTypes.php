<?php

Route::group([
    "namespace"  => "ProviderTypes",
], function () {
    /*
     * Admin ProviderTypes Controller
     */

    // Route for Ajax DataTable
    Route::get("providertypes/get", "AdminProviderTypesController@getTableData")->name("providertypes.get-list-data");

    Route::resource("providertypes", "AdminProviderTypesController");
});