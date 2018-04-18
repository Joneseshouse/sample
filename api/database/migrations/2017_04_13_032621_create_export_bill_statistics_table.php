<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExportBillStatisticsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('export_bill_daily', function (Blueprint $table) {
            $table->increments('id');
            $table->date('export_date');
            $table->integer('number_of_export')->unsigned()->default(0);
            $table->integer('number_of_bol')->unsigned()->default(0);
            $table->integer('sub_fee')->unsigned()->default(0);
            $table->integer('amount')->unsigned()->default(0);
            $table->integer('total')->unsigned()->default(0);
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
        Schema::dropIfExists('export_bill_statistics');
    }
}
