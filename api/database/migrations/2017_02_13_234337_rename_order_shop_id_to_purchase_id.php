<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameOrderShopIdToPurchaseId extends Migration
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
            $table->renameColumn('order_shop_id', 'purchase_id');
        });
        Schema::table('order_items', function (Blueprint $table) {
            //
            $table->renameColumn('order_shop_id', 'purchase_id');
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
