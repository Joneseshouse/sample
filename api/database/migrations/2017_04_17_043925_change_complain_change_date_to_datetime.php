<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeComplainChangeDateToDatetime extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bills_of_landing', function (Blueprint $table) {
            //
            $table->dropColumn('complain_change_date');
        });
        Schema::table('bills_of_landing', function (Blueprint $table) {
            //
            $table->dateTime('complain_change_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bills_of_landing', function (Blueprint $table) {
            //
        });
    }
}
