<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddComplainDetailToBillsOfLanding extends Migration
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
            $table->integer('complain_amount')->default(0);
            $table->boolean('complain_resolve')->default(true);
            $table->date('complain_change_date')->nullable();
            $table->string('complain_turn', 20)->nullable();
            $table->string('complain_type', 50)->nullable();
            $table->text('complain_note_user')->nullable();
            $table->text('complain_note_admin')->nullable();
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
