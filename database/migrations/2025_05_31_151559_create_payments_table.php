<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // The user who made/initiated the payment
            $table->morphs('payable'); // Polymorphic: e.g., User (for membership), TrainingRegistration, EventRegistration, Nomination
            $table->foreignId('payment_account_id_received_in')->nullable()->constrained('payment_accounts')->onDelete('set null'); // Account where payment was supposed to be made
            $table->decimal('amount', 12, 2); // Increased precision
            $table->string('currency', 3)->default('BDT');
            $table->string('purpose')->nullable(); // e.g., "Membership Registration Fee", "Training Batch X Fee"
            $table->string('external_transaction_id')->nullable()->index(); // User-provided TrxID
            $table->string('payment_method_used')->nullable(); // e.g., bKash Send Money, Bank Deposit Slip
            $table->string('payment_proof_path')->nullable(); // Screenshot/receipt path
            $table->timestamp('payment_date')->useCurrent(); // When user claims they paid
            $table->string('status')->default('pending_verification'); // pending_verification, verified, rejected, refunded, partially_refunded
            $table->foreignId('verified_by_user_id')->nullable()->constrained('users')->onDelete('set null'); // Admin/Finance Officer
            $table->timestamp('verified_at')->nullable();
            $table->text('verification_notes')->nullable(); // Reason for rejection, verification details etc.
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
