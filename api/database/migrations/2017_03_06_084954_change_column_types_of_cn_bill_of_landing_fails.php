<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeColumnTypesOfCnBillOfLandingFails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cn_bill_of_landing_fails', function (Blueprint $table) {
            //
            $table->dropColumn('packages');
            $table->dropColumn('input_mass');
            $table->dropColumn('length');
            $table->dropColumn('width');
            $table->dropColumn('height');
            $table->dropColumn('sub_fee');
        });
        Schema::table('cn_bill_of_landing_fails', function (Blueprint $table) {
            //
            $table->string('packages', 10)->nullable();
            $table->string('input_mass', 10)->nullable();
            $table->string('length', 10)->nullable();
            $table->string('width', 10)->nullable();
            $table->string('height', 10)->nullable();
            $table->string('sub_fee', 20)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cn_bill_of_landing_fails', function (Blueprint $table) {
            //
        });
    }
}
