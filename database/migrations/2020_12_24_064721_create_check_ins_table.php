<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCheckInsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('check_ins', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sparring_id')->nullable();
            $table->foreign('sparring_id')->references('id')->on('sparrings')->onDelete('cascade');
            $table->unsignedBigInteger('fight_id')->nullable();
            $table->foreign('fight_id')->references('id')->on('fights')->onDelete('cascade');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->dateTime('checkin')->nullable();
            $table->dateTime('checkout')->nullable();
            $table->boolean('is_confirmed')->nullable();
            $table->string('week')->nullable();
            $table->string('day')->nullable();
            $table->date('date')->nullable();
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
        Schema::dropIfExists('check_ins');
    }
}
