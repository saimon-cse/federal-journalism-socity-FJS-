<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
// database/migrations/xxxx_xx_xx_xxxxxx_create_job_postings_table.php
Schema::create('job_postings', function (Blueprint $table) {
    $table->id();
    $table->string('title');
    $table->text('description');
    $table->string('position_name');
    $table->integer('number_of_vacancies')->default(1);
    $table->text('qualifications_required');
    $table->string('salary_range')->nullable(); // e.g., "Negotiable", "30k-40k BDT"
    $table->string('job_location')->nullable();
    $table->enum('employment_type', ['full_time', 'part_time', 'contractual', 'internship'])->default('full_time');
    $table->string('company_name')->nullable(); // If posting for other companies
    $table->string('company_logo')->nullable();
    $table->timestamp('application_deadline');
    $table->string('banner_image')->nullable();
    $table->string('video_link')->nullable();
    $table->boolean('is_published')->default(false);
    $table->foreignId('posted_by_user_id')->constrained('users')->onDelete('cascade'); // Admin/User who posted
    $table->timestamps();
    $table->softDeletes();
});


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_postings');
    }
};
