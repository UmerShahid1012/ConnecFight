<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDisputeToFightMediaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fight_media', function (Blueprint $table) {
            $table->unsignedBigInteger('dispute_id')->nullable();
            $table->foreign('dispute_id')->references('id')->on('disputes')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fight_media', function (Blueprint $table) {
            //
        });
    }
}
