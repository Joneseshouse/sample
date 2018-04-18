<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCnBillOfLandingFails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cn_bill_of_landing_fails', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code', 100);
            $table->float('input_mass')->unsigned()->default(0);
            $table->integer('length')->unsigned()->default(0);
            $table->integer('width')->unsigned()->default(0);
            $table->integer('height')->unsigned()->default(0);
            $table->integer('packages')->unsigned()->default(1);
            $table->string('address_uid', 20);
            $table->integer('sub_fee')->default(0);
            $table->string('note')->nullable();
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
        Schema::dropIfExists('cn_bill_of_landing_fails');
    }
}
