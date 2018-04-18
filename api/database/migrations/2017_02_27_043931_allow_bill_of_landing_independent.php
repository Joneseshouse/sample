<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AllowBillOfLandingIndependent extends Migration
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
            $table->integer('order_id')->unsigned()->nullable()->change();
            $table->integer('purchase_id')->unsigned()->nullable()->change();
            $table->integer('address_id')->unsigned()->nullable();
            $table->integer('rate')->unsigned();
            $table->float('delivery_fee')->unsigned()->default(0);
            $table->float('delivery_fee_unit')->unsigned()->default(0);
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
