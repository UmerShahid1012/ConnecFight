<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFightFacilitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fight_facilities', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sparring_id')->nullable();
            $table->foreign('sparring_id')->references('id')->on('sparrings')->onDelete('cascade');
            $table->unsignedBigInteger('fight_id')->nullable();
            $table->foreign('fight_id')->references('id')->on('fights')->onDelete('cascade');
            $table->unsignedBigInteger('facility_id')->nullable();
            $table->foreign('facility_id')->references('id')->on('facilities')->onDelete('cascade');

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
        Schema::dropIfExists('fight_facilities');
    }
}
