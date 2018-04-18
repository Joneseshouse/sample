<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNormalOrderLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_order_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('uid', 30);
            $table->integer('user_id')->unsigned();
            $table->integer('admin_id')->unsigned()->nullable();
            $table->integer('order_id')->unsigned()->nullable();
            $table->integer('purchase_id')->unsigned()->nullable();
            $table->boolean('is_normal')->default(true);
            $table->string('target', 30); # order/purchase/billOfLanding
            $table->string('type', 20); # add/edit/remove/chat
            $table->text('content')->nullable();
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
        Schema::dropIfExists('user_order_logs');
    }
}
