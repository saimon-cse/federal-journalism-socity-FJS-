<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->string('phone_number')->unique()->nullable();
            $table->string('profile_picture_path')->nullable();
            $table->boolean('is_member')->default(false);
            $table->date('membership_start_date')->nullable();
            $table->date('membership_expires_on')->nullable();
            $table->boolean('newsletter_subscribed')->default(false);
            $table->boolean('is_profile_public')->default(false);
            $table->string('user_type')->default('general_applicant');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
};
