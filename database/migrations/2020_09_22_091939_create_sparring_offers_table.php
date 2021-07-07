<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSparringOffersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sparring_offers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sparring_id');
            $table->foreign('sparring_id')->references('id')->on('sparrings')->onDelete('cascade');
            $table->unsignedBigInteger('offer_by');
            $table->foreign('offer_by')->references('id')->on('users')->onDelete('cascade');

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
        Schema::dropIfExists('sparring_offers');
    }
}
