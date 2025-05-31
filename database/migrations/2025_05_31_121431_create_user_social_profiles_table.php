<?php

// database/migrations/xxxx_xx_xx_xxxxxx_create_user_social_profiles_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('user_social_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('platform_name'); // e.g., Facebook, LinkedIn, Twitter
            $table->string('username_or_id')->nullable();
            $table->string('profile_url')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_social_profiles');
    }
};
