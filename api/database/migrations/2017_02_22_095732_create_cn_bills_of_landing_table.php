<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCnBillsOfLandingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cn_bills_of_landing', function (Blueprint $table) {
            $table->increments('id');
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
        Schema::dropIfExists('cn_bills_of_landing');
    }
}
