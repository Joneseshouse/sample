<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAdminFullNameAndUserFullNameToUserOrderLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_order_logs', function (Blueprint $table) {
            //
            $table->string('admin_full_name')->nullable();
            $table->string('user_full_name')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_order_logs', function (Blueprint $table) {
            //
        });
    }
}
