<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAddressesMassPackagesToExportBolDaily extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('export_bill_daily', function (Blueprint $table) {
            //
            $table->string('addresses')->nullable();
            $table->float('mass')->default(0);
            $table->integer('packages')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('export_bill_daily', function (Blueprint $table) {
            //
        });
    }
}
