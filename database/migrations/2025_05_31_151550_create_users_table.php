<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Full name for display
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            // Profile photo path/details will be handled by Spatie Media Library or a simple string path
            $table->string('profile_photo')->nullable();
            $table->boolean('is_active')->default(true); // To activate/deactivate users
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });


        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();

            // Personal Details
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('father_name')->nullable();
            $table->string('mother_name')->nullable();
            $table->date('dob')->nullable(); // Date of Birth
            $table->string('blood_group', 5)->nullable();
            $table->string('gender', 15)->nullable(); // e.g., male, female, other, prefer_not_to_say
            $table->string('religion')->nullable();

            // Contact Details
            $table->string('phone_primary')->nullable()->unique();
            $table->string('phone_secondary')->nullable()->unique();
            $table->string('whatsapp_number')->nullable()->unique();

            // Identity Documents (Numbers here, files via Spatie Media Library on User model)
            $table->string('nid_number')->nullable()->unique();
            $table->string('passport_number')->nullable()->unique();

            // Other Preferences
            $table->boolean('newsletter_subscribed')->default(false);
            $table->boolean('is_profile_public')->default(false); // Controls visibility of certain profile parts
            $table->string('workplace_type')->nullable();


            $table->timestamps();
        });


        Schema::create('user_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('address_type'); // e.g., 'permanent', 'current_residential', 'work'
            $table->foreignId('division_id')->nullable()->constrained('divisions')->nullOnDelete();
            $table->foreignId('district_id')->nullable()->constrained('districts')->nullOnDelete();
            $table->foreignId('upazila_id')->nullable()->constrained('upazilas')->nullOnDelete();
            $table->string('street_address')->nullable();
            $table->string('postal_code')->nullable();
            $table->text('address_details')->nullable(); // For any extra details
            $table->boolean('is_primary')->default(false); // If a user has multiple of one type, which is primary
            $table->timestamps();

            $table->index(['user_id', 'address_type']);
        });

        Schema::create('user_educations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('degree_level'); // e.g., SSC, HSC, Bachelor's, Master's, PhD
            $table->string('degree_name'); // e.g., B.Sc. in CSE, M.A. in English
            $table->string('institution_name');
            $table->string('board_or_university')->nullable();
            $table->string('major_subject')->nullable();
            $table->year('passing_year')->nullable();
            $table->string('grade_or_cgpa')->nullable();
            $table->boolean('is_currently_studying')->default(false);
            $table->timestamps();
        });

        Schema::create('user_experiences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('organization_name');
            $table->string('designation'); // Position/Job Title
            $table->text('responsibilities')->nullable();
            $table->date('start_date');
            $table->date('end_date')->nullable(); // Null if currently working here
            $table->boolean('is_current_job')->default(false);
            $table->string('employment_type')->nullable(); // e.g., Full-time, Part-time, Contract
            $table->timestamps();
        });


        Schema::create('user_social_medias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('platform_name'); // e.g., Facebook, Twitter, LinkedIn, GitHub
            $table->string('profile_url')->unique(); // Or just username/handle if preferred
            $table->timestamps();

            $table->unique(['user_id', 'platform_name']); // A user has one link per platform
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('user_profiles');
        Schema::dropIfExists('user_addresses');
        Schema::dropIfExists('user_educations');
        Schema::dropIfExists('user_experiences');
        Schema::dropIfExists('user_social_medias');
        Schema::dropIfExists('users');
    }
};
