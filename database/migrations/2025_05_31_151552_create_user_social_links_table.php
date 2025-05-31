<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('user_social_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('platform_name');
            $table->string('profile_url');
            $table->timestamps();

            $table->unique(['user_id', 'platform_name']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_social_links');
    }
};
