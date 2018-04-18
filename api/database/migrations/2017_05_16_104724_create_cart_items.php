<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCartItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cart_items', function (Blueprint $table) {
            $table->softDeletes();
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->text('title');
            $table->string('properties')->nullable();
            $table->integer('quantity')->unsigned();
            $table->string('shop_name')->nullable();
            $table->string('shop_uid')->nullable();
            $table->string('avatar')->nullable();
            $table->float('unit_price')->unsigned();
            $table->text('message')->nullable();
            $table->text('url');
            $table->string('vendor');
            $table->integer('rate');
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
        Schema::dropIfExists('cart_items');
    }
}
