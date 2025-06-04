<?php
// database/migrations/xxxx_xx_xx_xxxxxx_create_elections_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('elections', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->date('election_date')->nullable();
            $table->timestamp('nomination_start_datetime');
            $table->timestamp('nomination_end_datetime')->nullable();
            $table->timestamp('withdrawal_end_datetime')->nullable();
            $table->timestamp('results_declared_at')->nullable();

            $table->foreignId('committee_id')->nullable()->comment('Election for a specific committee')->constrained('committees')->nullOnDelete();
            // OR broader geographic targeting if not committee-specific
            $table->enum('level', ['central', 'division', 'district', 'upazila', 'other'])->nullable();
            $table->foreignId('target_division_id')->nullable()->constrained('divisions')->nullOnDelete();
            $table->foreignId('target_district_id')->nullable()->constrained('districts')->nullOnDelete();
            $table->foreignId('target_upazila_id')->nullable()->constrained('upazilas')->nullOnDelete();

            $table->decimal('nomination_fee', 10, 2)->default(0.00);
            $table->foreignId('default_payment_account_id')->nullable()->comment('For nomination fees')->constrained('payment_accounts')->nullOnDelete();
            $table->enum('status', ['pending_setup', 'announced', 'nominations_open', 'nominations_closed', 'scrutiny', 'withdrawal_period', 'final_candidates', 'voting_open', 'voting_closed', 'results_declared', 'completed', 'cancelled'])->default('pending_setup');
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

         Schema::create('election_positions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('election_id')->constrained('elections')->cascadeOnDelete();
            // $table->string('name')->comment('e.g., President, General Secretary');
            $table->foreignId('position_id')->nullable()->constrained('committee_positions')->nullOnDelete(); // If this position is linked to a committee position
            $table->integer('number_of_seats')->default(1);
            $table->text('description')->nullable();
            $table->integer('sort_order')->default(0);
            $table->string('slug')->nullable(); // Added based on unique constraint: ['election_id', 'slug']
            $table->timestamps();

            $table->unique(['election_id', 'slug']);
        });

        Schema::create('election_nominations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('election_position_id')->constrained('election_positions')->cascadeOnDelete();
            $table->foreignId('user_id')->comment('Candidate User ID')->constrained('users')->cascadeOnDelete();
            $table->timestamp('application_datetime')->useCurrent();
            $table->enum('status', ['pending_payment', 'pending_approval', 'approved', 'rejected', 'withdrawn'])->default('pending_payment');
            $table->foreignId('payment_id')->nullable()->constrained('payments')->nullOnDelete();
            $table->foreignId('processed_by')->nullable()->comment('Admin who approved/rejected')->constrained('users')->nullOnDelete();
            $table->timestamp('processed_at')->nullable();
            $table->text('processing_remarks')->nullable()->comment('Reason for approval/rejection');
            $table->timestamp('withdrawn_at')->nullable();
            $table->text('withdrawal_reason')->nullable();
            $table->timestamps();

            $table->unique(['election_position_id', 'user_id']);
        });


    }

    public function down(): void
    {
        Schema::dropIfExists('election_nominations');
        Schema::dropIfExists('election_positions');
        Schema::dropIfExists('elections');
    }
};
