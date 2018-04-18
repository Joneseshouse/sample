<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMissingInfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lost', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('admin_id')->unsigned();
            $table->integer('bill_of_landing_id')->unsigned();
            $table->string('preview')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('chat_lost', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('lost_id')->unsigned();
            $table->integer('admin_id')->unsigned()->nullable();
            $table->integer('user_id')->unsigned()->nullable();
            $table->text('message');
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
        Schema::dropIfExists('lost');
    }
}
