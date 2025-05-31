<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->onDelete('cascade');
            $table->string('father_name')->nullable();
            $table->string('mother_name')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('blood_group', 5)->nullable();
            $table->string('gender')->nullable();
            $table->string('religion')->nullable();
            $table->string('whatsapp_number')->unique()->nullable();
            $table->string('nid_number')->unique()->nullable();
            $table->string('nid_path')->nullable();
            $table->string('passport_number')->unique()->nullable();
            $table->string('passport_path')->nullable();
            $table->string('workplace_type')->nullable();
            $table->text('bio')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_profiles');
    }
};
