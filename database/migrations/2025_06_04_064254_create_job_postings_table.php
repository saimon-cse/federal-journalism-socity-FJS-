<?php
// database/migrations/xxxx_xx_xx_xxxxxx_create_job_postings_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('job_postings', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->longText('description');
            $table->string('banner_image_path')->nullable(); // Or Spatie
            $table->date('application_deadline');
            $table->string('apply_instructions')->nullable();
            $table->foreignId('posted_by')->constrained('users')->cascadeOnDelete();
            $table->string('status')->default('draft'); // draft, published, interviewing, filled, closed, expired
            $table->timestamps();
        });


        Schema::create('job_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_posting_id')->constrained('job_postings')->cascadeOnDelete(); // Corrected from job_id to job_posting_id to match usage
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            // CV and NID files via Spatie Media Library on this model or linked to User
            $table->text('cv')->nullable();
            $table->string('status')->default('submitted'); // submitted, viewed, under_review, shortlisted, interviewed, offered, hired, rejected, withdrawn
            $table->decimal('expected_salary', 10, 2)->nullable();
            $table->date('available_start_date')->nullable();
            $table->text('notes_for_hiring_manager')->nullable(); // Internal notes by HR
            $table->timestamp('applied_at')->useCurrent();
            $table->timestamps();
            $table->unique(['job_posting_id', 'user_id']);
        });




    }
    public function down() {
        Schema::dropIfExists('job_applications');
         Schema::dropIfExists('job_postings');
    }
};
