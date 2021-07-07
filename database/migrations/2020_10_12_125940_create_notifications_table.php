<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->string('sender_name')->nullable();
            $table->bigInteger('user_id')->unsigned()->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->bigInteger('on_user')->unsigned();
            $table->foreign('on_user')->references('id')->on('users')->onDelete('cascade');
            $table->longText('notification_text')->nullable();
            $table->longText('unique_text')->nullable();
            $table->boolean('is_read')->default(0);
            $table->string('profile_photo')->nullable();
            $table->enum('type', ['message','follow','post','fight','daily_updates'])->nullable();
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
        Schema::dropIfExists('notifications');
    }
}
