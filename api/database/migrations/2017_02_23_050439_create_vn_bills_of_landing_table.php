<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVnBillsOfLandingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vn_bills_of_landing', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('order_id')->unsigned()->nullable();
            $table->integer('purchase_id')->unsigned()->nullable();
            $table->integer('user_id')->unsigned()->nullable();
            $table->integer('admin_id')->unsigned();
            $table->string('code', 100);
            $table->float('mass');
            $table->integer('packages')->default(1);
            $table->integer('sub_fee')->default(0);
            $table->string('note')->nullable();
            $table->string('order_type', 50)->nullable();
            $table->boolean('match')->default(false);
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
        Schema::dropIfExists('vn_bills_of_landing');
    }
}
