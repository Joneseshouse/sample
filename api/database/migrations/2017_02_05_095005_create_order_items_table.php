<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('order_id')->unsigned();
            $table->integer('shop_id')->unsigned();
            $table->string('title');
            $table->string('avatar')->nullable();
            $table->string('message')->nullable();
            $table->string('properties')->nullable();
            $table->integer('quantity')->unsigned();
            $table->integer('rate')->unsigned();
            $table->float('unit_price')->unsigned();
            $table->text('url');
            $table->string('vendor');
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
        Schema::dropIfExists('order_items');
    }
}
