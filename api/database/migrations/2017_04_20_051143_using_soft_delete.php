<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UsingSoftDelete extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        //
        Schema::table('categories', function ($table) {$table->softDeletes();});
        Schema::table('attaches', function ($table) {$table->softDeletes();});
        Schema::table('configs', function ($table) {$table->softDeletes();});
        Schema::table('shops', function ($table) {$table->softDeletes();});
        Schema::table('user_order_logs', function ($table) {$table->softDeletes();});
        Schema::table('login_failed_logs', function ($table) {$table->softDeletes();});
        Schema::table('migrations', function ($table) {$table->softDeletes();});
        Schema::table('dropdowns', function ($table) {$table->softDeletes();});
        Schema::table('bills_of_landing', function ($table) {$table->softDeletes();});
        Schema::table('order_items', function ($table) {$table->softDeletes();});
        Schema::table('permissions', function ($table) {$table->softDeletes();});
        Schema::table('users', function ($table) {$table->softDeletes();});
        Schema::table('export_bill_daily', function ($table) {$table->softDeletes();});
        Schema::table('role_types', function ($table) {$table->softDeletes();});
        Schema::table('bol_reports', function ($table) {$table->softDeletes();});
        Schema::table('roles', function ($table) {$table->softDeletes();});
        Schema::table('banks', function ($table) {$table->softDeletes();});
        Schema::table('bols_daily', function ($table) {$table->softDeletes();});
        Schema::table('banners', function ($table) {$table->softDeletes();});
        Schema::table('jobs', function ($table) {$table->softDeletes();});
        Schema::table('tokens', function ($table) {$table->softDeletes();});
        Schema::table('articles', function ($table) {$table->softDeletes();});
        Schema::table('vn_bills_of_landing', function ($table) {$table->softDeletes();});
        Schema::table('cn_bills_of_landing', function ($table) {$table->softDeletes();});
        Schema::table('collect_bols', function ($table) {$table->softDeletes();});
        Schema::table('purchases', function ($table) {$table->softDeletes();});
        Schema::table('admins', function ($table) {$table->softDeletes();});
        Schema::table('user_accounting', function ($table) {$table->softDeletes();});
        Schema::table('orders', function ($table) {$table->softDeletes();});
        Schema::table('area_codes', function ($table) {$table->softDeletes();});
        Schema::table('rate_logs', function ($table) {$table->softDeletes();});
        Schema::table('export_bills', function ($table) {$table->softDeletes();});
        Schema::table('check_bills', function ($table) {$table->softDeletes();});
        Schema::table('check_items', function ($table) {$table->softDeletes();});
        Schema::table('cn_bill_of_landing_fails', function ($table) {$table->softDeletes();});
        Schema::table('addresses', function ($table) {$table->softDeletes();});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        //
        Schema::table('categories', function ($table) {$table->dropColumn('deleted_at');});
        Schema::table('attaches', function ($table) {$table->dropColumn('deleted_at');});
        Schema::table('configs', function ($table) {$table->dropColumn('deleted_at');});
        Schema::table('shops', function ($table) {$table->dropColumn('deleted_at');});
        Schema::table('user_order_logs', function ($table) {$table->dropColumn('deleted_at');});
        Schema::table('login_failed_logs', function ($table) {$table->dropColumn('deleted_at');});
        Schema::table('migrations', function ($table) {$table->dropColumn('deleted_at');});
        Schema::table('dropdowns', function ($table) {$table->dropColumn('deleted_at');});
        Schema::table('bills_of_landing', function ($table) {$table->dropColumn('deleted_at');});
        Schema::table('order_items', function ($table) {$table->dropColumn('deleted_at');});
        Schema::table('permissions', function ($table) {$table->dropColumn('deleted_at');});
        Schema::table('users', function ($table) {$table->dropColumn('deleted_at');});
        Schema::table('export_bill_daily', function ($table) {$table->dropColumn('deleted_at');});
        Schema::table('role_types', function ($table) {$table->dropColumn('deleted_at');});
        Schema::table('bol_reports', function ($table) {$table->dropColumn('deleted_at');});
        Schema::table('roles', function ($table) {$table->dropColumn('deleted_at');});
        Schema::table('banks', function ($table) {$table->dropColumn('deleted_at');});
        Schema::table('bols_daily', function ($table) {$table->dropColumn('deleted_at');});
        Schema::table('banners', function ($table) {$table->dropColumn('deleted_at');});
        Schema::table('jobs', function ($table) {$table->dropColumn('deleted_at');});
        Schema::table('tokens', function ($table) {$table->dropColumn('deleted_at');});
        Schema::table('articles', function ($table) {$table->dropColumn('deleted_at');});
        Schema::table('vn_bills_of_landing', function ($table) {$table->dropColumn('deleted_at');});
        Schema::table('cn_bills_of_landing', function ($table) {$table->dropColumn('deleted_at');});
        Schema::table('collect_bols', function ($table) {$table->dropColumn('deleted_at');});
        Schema::table('purchases', function ($table) {$table->dropColumn('deleted_at');});
        Schema::table('admins', function ($table) {$table->dropColumn('deleted_at');});
        Schema::table('user_accounting', function ($table) {$table->dropColumn('deleted_at');});
        Schema::table('orders', function ($table) {$table->dropColumn('deleted_at');});
        Schema::table('area_codes', function ($table) {$table->dropColumn('deleted_at');});
        Schema::table('rate_logs', function ($table) {$table->dropColumn('deleted_at');});
        Schema::table('export_bills', function ($table) {$table->dropColumn('deleted_at');});
        Schema::table('check_bills', function ($table) {$table->dropColumn('deleted_at');});
        Schema::table('check_items', function ($table) {$table->dropColumn('deleted_at');});
        Schema::table('cn_bill_of_landing_fails', function ($table) {$table->dropColumn('deleted_at');});
        Schema::table('addresses', function ($table) {$table->dropColumn('deleted_at');});
    }
}
