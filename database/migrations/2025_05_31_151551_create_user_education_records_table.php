<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('user_education_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('degree_level');
            $table->string('degree_title');
            $table->string('major_subject')->nullable();
            $table->string('institution_name');
            $table->unsignedSmallInteger('graduation_year')->nullable();
            $table->string('result_grade')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_education_records');
    }
};
