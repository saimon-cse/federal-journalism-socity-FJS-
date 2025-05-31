<?php

// database/migrations/xxxx_xx_xx_xxxxxx_create_user_education_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('user_education', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('degree_name'); // e.g., SSC, HSC, B.A.
            $table->string('subject_or_group')->nullable();
            $table->string('institution_name');
            $table->year('passing_year')->nullable();
            $table->string('result')->nullable(); // e.g., GPA, Division
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_education');
    }
};
