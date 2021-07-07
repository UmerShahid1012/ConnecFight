<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTransactionsTableAddStatusIsTransferSubCatIds extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->boolean('is_withdraw')->nullable();
            $table->integer('total_amount')->nullable();
            $table->double('percentage')->nullable();
            $table->unsignedBigInteger('sub_tag_id')->nullable();
            $table->foreign('sub_tag_id')->references('id')->on('sub_tags')->onDelete('cascade');
            $table->unsignedBigInteger('status')->nullable();
            $table->foreign('status')->references('id')->on('statuses')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
}
