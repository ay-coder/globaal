<?php

Route::group([
    "namespace"  => "CompanyServices",
], function () {
    /*
     * Admin CompanyServices Controller
     */

    // Route for Ajax DataTable
    Route::get("companyservices/get", "AdminCompanyServicesController@getTableData")->name("companyservices.get-list-data");

    Route::resource("companyservices", "AdminCompanyServicesController");
});