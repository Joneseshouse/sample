<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_transactions', function (Blueprint $table) {
            $table->softDeletes();
            $table->increments('id');
            $table->string('uid');
            $table->integer('admin_id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->integer('order_id')->unsigned()->nullable();
            $table->integer('receipt_id')->unsigned()->nullable();
            $table->boolean('latest')->default(false);

            $table->string('type');
            $table->string('money_type');

            $table->bigInteger('amount')->default(0);
            $table->bigInteger('credit_balance')->default(0);
            $table->bigInteger('liabilities')->default(0);
            $table->bigInteger('balance')->default(0);
            $table->bigInteger('purchasing')->default(0);
            $table->bigInteger('missing')->default(0);

            $table->string('image')->nullable();
            $table->text('note');
            
            $table->integer('ordinal')->default(0);
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
        Schema::dropIfExists('user_transactions');
    }
}
