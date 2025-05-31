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
// database/migrations/xxxx_xx_xx_xxxxxx_create_training_enrollments_table.php
Schema::create('training_enrollments', function (Blueprint $table) {
    $table->id();
    $table->foreignId('training_id')->constrained()->onDelete('cascade');
    $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Participant
    $table->unsignedBigInteger('payment_id')->nullable()->index(); // Link to payments table if paid
    $table->enum('status', ['pending_payment', 'enrolled', 'completed', 'cancelled'])->default('pending_payment');
    $table->timestamp('enrolled_at')->nullable();
    $table->string('certificate_path')->nullable(); // Path to generated certificate
    $table->string('id_card_path')->nullable(); // Path to generated ID card
    $table->timestamps();
    $table->unique(['training_id', 'user_id']); // A user can enroll in a training only once
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('training_enrollments');
    }
};
