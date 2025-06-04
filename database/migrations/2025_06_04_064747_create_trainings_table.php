<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('trainings', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('batch_identifier')->nullable()->comment('e.g., "Batch 005", "Spring 2024 Cohort"');
            // Foreign Key to categories table (type: 'training_subject')
            $table->foreignId('training_subject_category_id')->constrained('categories')->cascadeOnDelete();

            $table->text('description')->nullable();
            $table->enum('mode', ['online', 'offline', 'hybrid']);
            $table->text('venue_details')->nullable()->comment('Address for offline, or platform details for online.');

            $table->timestamp('start_datetime');
            $table->timestamp('end_datetime')->nullable();
            $table->integer('total_duration_hours')->nullable()->comment('Estimated total contact hours.');
            $table->integer('total_sessions')->nullable()->comment('Estimated number of distinct sessions.');

            $table->enum('payment_type', ['paid', 'free'])->default('free');
            $table->decimal('fee_amount', 10, 2)->nullable()->comment('Applicable if payment_type is "paid".');
            // Foreign Key to payment_accounts table
            $table->foreignId('payment_collection_account_id')->nullable()->comment('Org account for fee collection')->constrained('payment_accounts')->nullOnDelete();

            // Target Audience Booleans
            $table->boolean('is_open_for_members')->default(true);
            $table->boolean('is_open_for_non_members')->default(false);
            // JSON columns for target regions previously here are REMOVED

            $table->integer('max_participants')->nullable()->comment('Maximum number of participants allowed.');
            $table->timestamp('application_deadline')->nullable();

            $table->enum('status', ['draft', 'published', 'accepting_applications', 'ongoing', 'completed', 'cancelled', 'postponed'])->default('draft');
            $table->string('video_youtube_url')->nullable();
            // JSON column for certificate_signatories previously here is REMOVED

            // These can still store JSON as text if their structure is complex and not directly queried relationally
            $table->text('custom_id_card_settings')->nullable()->comment('JSON config for ID card template, if training-specific.');
            $table->text('custom_certificate_settings')->nullable()->comment('JSON config for certificate template, if training-specific.');

            $table->foreignId('created_by_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('copied_from_training_id')->nullable()->comment('If this training was cloned from another')->constrained('trainings')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('training_target_divisions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('training_id')->constrained('trainings')->cascadeOnDelete();
            $table->foreignId('division_id')->constrained('divisions')->cascadeOnDelete(); // Assumes 'divisions' table exists
            $table->timestamps();

            $table->unique(['training_id', 'division_id'], 'training_division_unique');
        });
        Schema::create('training_target_districts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('training_id')->constrained('trainings')->cascadeOnDelete();
            $table->foreignId('district_id')->constrained('districts')->cascadeOnDelete(); // Assumes 'districts' table exists
            $table->timestamps();

            $table->unique(['training_id', 'district_id'], 'training_district_unique');
        });

        Schema::create('training_certificate_signatories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('training_id')->constrained('trainings')->cascadeOnDelete();
            $table->string('name');
            $table->string('designation');
            $table->integer('sort_order')->default(0)->comment('Order in which signatories appear on the certificate.');
            $table->timestamps();
        });

        Schema::create('resource_persons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->text('bio')->nullable()->comment('A brief biography of the resource person.');
            $table->boolean('is_publicly_listed')->default(false)->comment('If true, profile can be shown on the public website section.');
            $table->decimal('standard_honorarium_rate', 10, 2)->nullable()->comment('Standard rate, can be per hour, per session etc., context dependent.');
            $table->text('preferred_payment_details')->nullable()->comment('Bank account, mobile finance, etc. for receiving honorarium.');
            $table->enum('status', ['pending_approval', 'active', 'inactive', 'rejected'])->default('pending_approval');
            $table->text('admin_remarks')->nullable()->comment('Internal notes or reasons for status by admin.');
            $table->foreignId('approved_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });


        Schema::create('resource_person_expertise', function (Blueprint $table) {
            $table->id();
            $table->foreignId('resource_person_id')->constrained('resource_persons')->cascadeOnDelete();
            // Foreign Key to categories table (type: 'resource_person_expertise')
            $table->foreignId('category_id')->comment('FK to categories.id (expertise area)')->constrained('categories')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['resource_person_id', 'category_id'], 'rp_expertise_unique');
        });

        Schema::create('resource_person_contents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('resource_person_id')->constrained('resource_persons')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            // File (presentation, document) handled by Spatie Media Library
            $table->enum('status', ['pending_approval', 'approved', 'requires_modification', 'rejected'])->default('pending_approval');
            $table->foreignId('reviewed_by_user_id')->nullable()->comment('Admin who reviewed')->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('admin_remarks')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('training_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('training_id')->constrained('trainings')->cascadeOnDelete();
            $table->string('title')->comment('Title of this specific session/module.');
            $table->text('description')->nullable();
            $table->timestamp('session_datetime_start');
            $table->timestamp('session_datetime_end')->nullable();
            $table->integer('duration_minutes')->nullable();
            $table->foreignId('instructor_id')->nullable()->constrained('users')->nullOnDelete(); // User ID of assigned Resource Person
            $table->text('session_materials')->nullable()->comment('Link to materials or description.');
            $table->string('meeting_link')->nullable()->comment('For online/hybrid sessions.');
            $table->integer('rank')->default(0);
            $table->timestamps();
        });

        Schema::create('training_instructors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('training_id')->constrained('trainings')->cascadeOnDelete();
            // User ID of the instructor (must be a Resource Person and have a record in 'users' table)
            $table->foreignId('instructor_id')->comment('User ID of the instructor')->constrained('users')->cascadeOnDelete(); // Changed from instructor_user_id
            $table->string('role_in_training')->nullable()->default('Instructor')->comment('e.g., Lead Instructor, Guest Speaker, Facilitator');
            $table->timestamps();
            $table->unique(['training_id', 'instructor_id'], 'training_instructor_unique'); // Changed from instructor_user_id
        });

        Schema::create('training_registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('training_id')->constrained('trainings')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete(); // The applicant
            $table->foreignId('payment_id')->nullable()->constrained('payments')->nullOnDelete();
            $table->enum('status', [
                'pending_payment', 'pending_approval', 'confirmed', 'waitlisted',
                'attended', 'completed_course', 'certificate_issued',
                'cancelled_by_user', 'rejected_by_admin',
            ])->default('pending_approval');
            $table->timestamp('application_date')->useCurrent();
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('admin_remarks')->nullable()->comment('Reason for rejection or other notes.');
            $table->timestamp('certificate_issued_at')->nullable();
            // Generated ID card & Certificate handled by Spatie Media Library
            $table->timestamps();
            $table->unique(['training_id', 'user_id'], 'training_registration_unique');
        });

        Schema::create('training_invitations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('training_id')->constrained('trainings')->cascadeOnDelete();
            // User ID of the Resource Person being invited
            $table->foreignId('resource_person_id')->comment('User ID of the Resource Person being invited')->constrained('users')->cascadeOnDelete(); // Changed from resource_person_user_id
            $table->foreignId('invited_by')->comment('Admin who sent the invitation')->constrained('users')->cascadeOnDelete();
            $table->text('invitation_message')->nullable();
            $table->decimal('offered_honorarium', 10, 2)->nullable();
            $table->enum('status', ['pending', 'accepted', 'declined', 'negotiating'])->default('pending');
            $table->timestamp('responded_at')->nullable();
            $table->text('response_remarks')->nullable()->comment('Remarks from Resource Person or Admin');
            $table->timestamps();
            $table->unique(['training_id', 'resource_person_id'], 'training_rp_invite_unique'); // Changed from resource_person_user_id
        });
    }

    public function down() {
        Schema::dropIfExists('training_invitations');
        Schema::dropIfExists('training_registrations');
        Schema::dropIfExists('training_instructors');
        Schema::dropIfExists('training_sessions');
        Schema::dropIfExists('resource_person_contents');
        Schema::dropIfExists('resource_person_expertise');
        Schema::dropIfExists('resource_persons');
        Schema::dropIfExists('training_certificate_signatories');
        Schema::dropIfExists('training_target_districts');
        Schema::dropIfExists('training_target_divisions');
        Schema::dropIfExists('trainings');
    }
};
