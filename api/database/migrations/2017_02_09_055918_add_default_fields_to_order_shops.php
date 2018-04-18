<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDefaultFieldsToOrderShops extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_shops', function (Blueprint $table) {
            //
            $table->integer('delivery_fee_unit')->default(30000);
        });
        Schema::table('orders', function (Blueprint $table) {
            //
            $table->float('order_fee')->default(5);
            $table->float('deposit_factor')->default(50);
            $table->float('complain_day')->default(2);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_shops', function (Blueprint $table) {
            //
        });
    }
}
