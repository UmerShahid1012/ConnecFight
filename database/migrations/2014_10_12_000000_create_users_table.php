<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('social_id')->nullable();
            $table->string('social_type')->nullable();
            $table->string('gender')->nullable();
            $table->string('weight')->nullable();
            $table->string('record')->nullable();
            $table->string('country')->nullable();
            $table->string('state')->nullable();
            $table->string('city')->nullable();
            $table->string('height')->nullable();
//            $table->string('federal_id')->nullable();
            $table->unsignedBigInteger('stance_id')->nullable();
            $table->foreign('stance_id')->references('id')->on('stances')->onDelete('cascade');
            $table->text('bio')->nullable();
            $table->boolean('is_active')->default(false);
            $table->boolean('is_verified')->default(false);
            $table->timestamp('last_login_at')->nullable();
            $table->text('profile_image')->nullable();
            $table->text('stripe_payout_account_id')->nullable();
            $table->boolean('is_bank_account_verified')->default(false);
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
