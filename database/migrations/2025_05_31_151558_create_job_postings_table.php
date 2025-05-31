<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('job_postings', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->unsignedInteger('number_of_positions')->default(1);
            $table->text('educational_qualification');
            $table->string('salary_range')->nullable();
            $table->text('description');
            $table->string('job_type')->default('full_time');
            $table->string('location')->nullable();
            $table->string('cover_image_path')->nullable();
            $table->string('youtube_link')->nullable();
            $table->date('application_deadline');
            $table->foreignId('posted_by_user_id')->constrained('users')->onDelete('cascade');
            $table->string('status')->default('open');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('job_postings');
    }
};
