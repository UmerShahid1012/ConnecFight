<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('stripe_plan_id')->nullable();
            $table->string('title')->nullable();
            $table->string('price')->nullable();
            $table->string('no_of_sparrings')->nullable();
            $table->string('no_of_applications')->nullable();
            $table->string('no_of_challenges')->nullable();
            $table->string('tax')->nullable();
            $table->string('tax_id')->nullable();
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
        Schema::dropIfExists('plans');
    }
}
