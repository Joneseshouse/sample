<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNewFieldsToRateLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rate_logs', function (Blueprint $table) {
            //
            $table->integer('buy_rate')->unsigned()->default(3400);
            $table->integer('sell_rate')->unsigned()->default(3400);
            $table->integer('order_rate')->unsigned()->default(3400);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rate_logs', function (Blueprint $table) {
            //
        });
    }
}
