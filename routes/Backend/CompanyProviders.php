<?php

Route::group([
    "namespace"  => "CompanyProviders",
], function () {
    /*
     * Admin CompanyProviders Controller
     */

    // Route for Ajax DataTable
    Route::get("companyproviders/get", "AdminCompanyProvidersController@getTableData")->name("companyproviders.get-list-data");

    Route::resource("companyproviders", "AdminCompanyProvidersController");
});