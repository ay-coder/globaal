<?php

Route::group([
    "namespace"  => "Appointments",
], function () {
    /*
     * Admin Appointments Controller
     */

    // Route for Ajax DataTable
    Route::get("appointments/get", "AdminAppointmentsController@getTableData")->name("appointments.get-list-data");

    Route::resource("appointments", "AdminAppointmentsController");
});