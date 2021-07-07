<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReviewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('given_by');
            $table->foreign('given_by')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedBigInteger('given_to');
            $table->foreign('given_to')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedBigInteger('sparring_id')->nullable();
            $table->foreign('sparring_id')->references('id')->on('sparrings')->onDelete('cascade');
            $table->unsignedBigInteger('fight_id')->nullable();
            $table->foreign('fight_id')->references('id')->on('fights')->onDelete('cascade');
            $table->text('comment')->nullable();
            $table->text('rating')->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('reviews');
    }
}
