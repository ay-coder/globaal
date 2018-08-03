<?php

Route::group([
    "namespace"  => "MasterCategories",
], function () {
    /*
     * Admin MasterCategories Controller
     */

    // Route for Ajax DataTable
    Route::get("mastercategories/get", "AdminMasterCategoriesController@getTableData")->name("mastercategories.get-list-data");

    Route::resource("mastercategories", "AdminMasterCategoriesController");
});