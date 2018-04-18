<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderShopsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_shops', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('order_id')->unsigned();
            $table->integer('shop_id')->unsigned();
            $table->string('bill_code', 50)->nullable();
            $table->float('amount')->default(0);
            $table->float('real_amount')->default(0);
            $table->float('delivery_fee')->default(0);
            $table->float('total')->default(0);
            $table->float('mass')->default(0);
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
        Schema::dropIfExists('order_shops');
    }
}
