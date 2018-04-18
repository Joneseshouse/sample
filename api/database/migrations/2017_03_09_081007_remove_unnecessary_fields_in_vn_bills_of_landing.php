<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveUnnecessaryFieldsInVnBillsOfLanding extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vn_bills_of_landing', function (Blueprint $table) {
            //
            $table->dropColumn('mass');
            $table->dropColumn('order_id');
            $table->dropColumn('packages');
            $table->dropColumn('purchase_id');
            $table->dropColumn('sub_fee');
            $table->dropColumn('user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vn_bills_of_landing', function (Blueprint $table) {
            //
        });
    }
}
