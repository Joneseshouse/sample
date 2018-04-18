<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->increments('id');
            $table->string('uid', 50);
            $table->float('rate')->unsigned()->default(3.4);
            $table->float('mass')->unsigned()->default(0);
            $table->integer('occur_fee')->unsigned()->default(0);
            $table->integer('service_fee')->unsigned()->default(0);
            $table->integer('amount')->unsigned()->default(0);
            $table->integer('total')->unsigned()->default(0);
            $table->integer('month')->unsigned()->default(0);
            $table->integer('year')->unsigned()->default(0);
            $table->string('status')->default('new');
            $table->string('type')->default('normal');
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
        Schema::dropIfExists('orders');
    }
}
