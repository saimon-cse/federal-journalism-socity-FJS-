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
// database/migrations/xxxx_xx_xx_xxxxxx_create_job_applications_table.php
Schema::create('job_applications', function (Blueprint $table) {
    $table->id();
    $table->foreignId('job_posting_id')->constrained('job_postings')->onDelete('cascade');
    $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Applicant
    $table->string('cv_file_path');
    $table->string('nid_file_path')->nullable(); // If required
    $table->text('cover_letter')->nullable();
    $table->enum('status', ['applied', 'shortlisted', 'interviewed', 'rejected', 'hired'])->default('applied');
    $table->timestamp('applied_at')->useCurrent();
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_applications');
    }
};
