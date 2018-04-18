<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAdminTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admin_transactions', function (Blueprint $table) {
            $table->softDeletes();
            $table->increments('id');
            $table->string('uid');
            $table->integer('admin_id')->unsigned();
            $table->integer('target_admin_id')->unsigned()->nullable();
            $table->boolean('latest')->default(false);

            $table->string('type');
            $table->string('money_type');

            $table->bigInteger('amount')->default(0);
            $table->bigInteger('credit_balance')->default(0);
            $table->bigInteger('liabilities')->default(0);
            $table->bigInteger('balance')->default(0);

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
        Schema::dropIfExists('admin_transactions');
    }
}
