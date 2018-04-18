<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCheckBillsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('check_bills', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('order_id')->unsigned()->nullable();
            $table->integer('purchase_id')->unsigned()->nullable();
            $table->integer('bill_of_landing_id')->unsigned();
            $table->integer('admin_id')->unsigned();
            $table->float('mass')->unsigned();
            $table->integer('packages')->unsigned()->default(1);
            $table->text('note')->nullable();
            $table->timestamps();
        });
        Schema::create('check_items', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('bill_of_landing_id')->unsigned();
            $table->integer('order_item_id')->unsigned();
            $table->integer('quantity')->unsigned();
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
        Schema::dropIfExists('check_bills');
    }
}
