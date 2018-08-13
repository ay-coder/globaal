<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableDataSchedules extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('data_schedules', function (Blueprint $table) 
        {
            $table->increments('id');
            $table->integer('user_id')->nullable();
            $table->integer('provider_id')->nullable();
            $table->integer('service_id')->nullable();
            $table->integer('company_id')->nullable();
            $table->string('day_name')->nullable();
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->integer('status')->default(1)->nullable();
            $table->timestamps();
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
