<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBolsDaily extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bols_daily', function (Blueprint $table) {
            $table->increments('id');
            $table->date('report_date');
            $table->integer('number_of_bols');
            $table->integer('order_bols');
            $table->integer('deposit_bols');
            $table->integer('missing_bols');
            $table->float('mass');
            $table->integer('total');
            $table->dateTime('last_updated')->nullable();
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
        Schema::dropIfExists('bols_daily');
    }
}
