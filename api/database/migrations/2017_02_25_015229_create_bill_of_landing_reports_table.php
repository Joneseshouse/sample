<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBillOfLandingReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bol_reports', function (Blueprint $table) {
            $table->increments('id');
            $table->date('report_date');
            $table->integer('number_of_bills')->default(0);
            $table->integer('number_of_packages')->default(0);
            $table->float('total_mass')->default(0);
            $table->integer('cn_normal')->default(0);
            $table->integer('cn_deposit')->default(0);
            $table->integer('cn_missing_info')->default(0);
            $table->integer('vn_normal')->default(0);
            $table->integer('vn_deposit')->default(0);
            $table->integer('vn_missing_info')->default(0);
            $table->integer('export_normal')->default(0);
            $table->integer('export_deposit')->default(0);
            $table->integer('export_missing_info')->default(0);
            $table->integer('inventory')->default(0);
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
        Schema::dropIfExists('bol_daily_reports');
    }
}
