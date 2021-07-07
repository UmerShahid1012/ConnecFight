<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFightsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fights', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('challenger');
            $table->foreign('challenger')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedBigInteger('defender');
            $table->foreign('defender')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedBigInteger('posted_by');
            $table->foreign('posted_by')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedBigInteger('event_id');
            $table->foreign('event_id')->references('id')->on('events')->onDelete('cascade');
            $table->unsignedBigInteger('winner_id')->nullable();
            $table->foreign('winner_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('event_host')->nullable();
            $table->string('location')->nullable();
            $table->date('match_date')->nullable();
            $table->string('fund')->nullable();
            $table->string('no_of_rounds')->nullable();
            $table->string('sports')->nullable();
            $table->text('description')->nullable();
            $table->unsignedBigInteger('status');
            $table->foreign('status')->references('id')->on('statuses')->onDelete('cascade');
            $table->boolean('fighter_one_accepted')->default(0);
            $table->boolean('fighter_two_accepted')->default(0);
            $table->boolean('is_ko')->default(0);
            $table->unsignedBigInteger('winner')->nullable();
            $table->foreign('winner')->references('id')->on('users')->onDelete('cascade');
            $table->dateTime('start')->nullable();
            $table->dateTime('end')->nullable();
            $table->boolean('is_overweight')->nullable();
            $table->timestamps();
            $table->softDeletes();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fights');
    }
}
