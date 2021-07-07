<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFightMediaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fight_media', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sparring_id')->nullable();
            $table->foreign('sparring_id')->references('id')->on('sparrings')->onDelete('cascade');
            $table->unsignedBigInteger('fight_id')->nullable();
            $table->foreign('fight_id')->references('id')->on('fights')->onDelete('cascade');
            $table->unsignedBigInteger('highlights_id')->nullable();
            $table->foreign('highlights_id')->references('id')->on('highlights')->onDelete('cascade');
            $table->text('media')->nullable();
            $table->text('thumbnail')->nullable();
            $table->string('type')->nullable();
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
        Schema::dropIfExists('fight_media');
    }
}
