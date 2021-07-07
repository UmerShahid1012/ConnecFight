<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('from')->nullable();
            $table->foreign('from')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedBigInteger('to')->nullable();
            $table->foreign('to')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedBigInteger('fight_id')->nullable();
            $table->foreign('fight_id')->references('id')->on('fights')->onDelete('cascade');
            $table->unsignedBigInteger('sparring_id')->nullable();
            $table->foreign('sparring_id')->references('id')->on('sparrings')->onDelete('cascade');
            $table->string('amount');
            $table->string('charge_id');
            $table->string('type');
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
        Schema::dropIfExists('transactions');
    }
}
