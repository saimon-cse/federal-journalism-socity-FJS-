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
// database/migrations/xxxx_xx_xx_xxxxxx_create_allowance_applications_table.php
Schema::create('allowance_applications', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Applicant
    $table->foreignId('allowance_type_id')->constrained('allowance_types')->onDelete('cascade');
    $table->text('application_details'); // Reason, required amount, etc.
    $table->decimal('requested_amount', 10, 2)->nullable();
    $table->decimal('approved_amount', 10, 2)->nullable();
    $table->enum('status', ['pending', 'under_review', 'approved', 'rejected', 'disbursed'])->default('pending');
    $table->foreignId('reviewed_by_user_id')->nullable()->constrained('users')->onDelete('set null');
    $table->timestamp('reviewed_at')->nullable();
    $table->foreignId('approved_by_user_id')->nullable()->constrained('users')->onDelete('set null');
    $table->timestamp('approved_at')->nullable();
    $table->text('rejection_reason')->nullable();
    $table->foreignId('disbursement_payment_id')->nullable()->constrained('payments')->onDelete('set null'); // If disbursed through a payment record
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('allowance_applications');
    }
};
