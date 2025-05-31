<?php


// database/migrations/xxxx_xx_xx_xxxxxx_create_payments_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // User who made the payment
            $table->string('payable_type')->nullable(); // e.g., App\Models\TrainingEnrollment, App\Models\MembershipFee, App\Models\EventRegistration
            $table->unsignedBigInteger('payable_id')->nullable(); // ID of the related model
            $table->string('purpose'); // e.g., "Membership Fee Jan 2024", "Training: XYZ Batch 2", "Election Nomination Fee"
            $table->decimal('amount', 10, 2);
            $table->string('transaction_id')->nullable()->index(); // User submitted transaction ID
            $table->string('payment_proof_path')->nullable(); // Path to uploaded proof
            $table->enum('status', ['pending_verification', 'verified', 'failed', 'refunded'])->default('pending_verification');
            $table->timestamp('payment_date')->nullable(); // Date user made the payment
            $table->foreignId('verified_by_user_id')->nullable()->constrained('users')->onDelete('set null'); // Admin who verified
            $table->timestamp('verified_at')->nullable();
            $table->text('remarks')->nullable(); // Admin remarks on verification
            $table->timestamps();

            $table->index(['payable_type', 'payable_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('payments');
    }
};
