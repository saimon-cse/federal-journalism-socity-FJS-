# Command to generate the migration file
php artisan make:migration create_complaints_suggestions_table --create=complaints_suggestions

# Content for the generated database/migrations/YYYY_MM_DD_HHMMSS_create_complaints_suggestions_table.php file:
<?php
// database/migrations/xxxx_xx_xx_xxxxxx_create_complaints_suggestions_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('complaints_suggestions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->comment('User who submitted, if logged in and not anonymous')->constrained('users')->nullOnDelete();
            $table->string('submitted_by_name')->nullable()->comment('Name if anonymous or for record keeping');
            $table->string('submitted_by_email')->nullable()->comment('Email if anonymous or for record keeping');
            $table->enum('type', ['complaint', 'suggestion', 'feedback']);
            $table->string('subject');
            $table->text('description');
            $table->boolean('is_anonymous')->default(false);
            $table->enum('status', ['new', 'opened', 'under_review', 'awaiting_reply', 'resolved', 'closed', 'rejected'])->default('new');
            $table->text('admin_remarks')->nullable();
            $table->foreignId('resolved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('resolved_at')->nullable();
            // Attachments should use Spatie Media Library on this model
            $table->timestamps();
            $table->softDeletes();
        });


        Schema::create('complaint_suggestion_replies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('complaint_id')->constrained('complaints_suggestions')->cascadeOnDelete();
            $table->foreignId('user_id')->comment('User who replied (can be submitter or agent)')->constrained('users')->cascadeOnDelete();
            $table->longText('reply_content');
            $table->string('reply_type')->default('agent'); // agent, user
            // Attachments for reply via Spatie Media Library
            // $table->string('attachment_path')->nullable(); // Path to any file attached with the reply

            $table->timestamps();
        });



    }

    public function down(): void
    {
        Schema::dropIfExists('complaint_suggestion_replies');
        Schema::dropIfExists('complaints_suggestions');
    }
};
