<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAcceptToDataCompanyProviders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('data_company_providers', function (Blueprint $table) 
        {
            $table->integer('accept_by_provider')->after('company_id')->default(0)->nullable();
            $table->integer('accept_by_company')->after('accept_by_provider')->default(0)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
