<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AllowNullAddressUidFromCnBillOfLandingFails extends Migration
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
            $table->string('address_uid', 20)->nullable()->change();
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
