<?php

Route::group([
    "namespace"  => "Patient",
], function () {
    /*
     * Admin Patient Controller
     */

    // Route for Ajax DataTable
    Route::get("patient/get", "AdminPatientController@getTableData")->name("patient.get-list-data");

    Route::resource("patient", "AdminPatientController");
});