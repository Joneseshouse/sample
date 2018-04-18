<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPackagesToBillOfLanding extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bills_of_landing', function (Blueprint $table) {
            //
            $table->integer('packages')->unsigned()->default(1);
            $table->integer('length')->unsigned()->default(1);
            $table->integer('width')->unsigned()->default(1);
            $table->integer('height')->unsigned()->default(1);
            $table->integer('transform_factor')->unsigned()->default(6000);
        });
        Schema::table('purchases', function (Blueprint $table) {
            //
            $table->float('inland_delivery_fee')->unsigned()->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bills_of_landing', function (Blueprint $table) {
            //
        });
    }
}
