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
// database/migrations/xxxx_xx_xx_xxxxxx_create_election_nominations_table.php
Schema::create('election_nominations', function (Blueprint $table) {
    $table->id();
    $table->foreignId('election_id')->constrained()->onDelete('cascade');
    $table->foreignId('election_position_id')->constrained('election_positions')->onDelete('cascade');
    $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Candidate
    $table->foreignId('payment_id')->nullable()->constrained('payments')->onDelete('set null'); // Nomination fee payment
    $table->text('manifesto_summary')->nullable();
    $table->string('symbol_preference')->nullable(); // If applicable
    $table->enum('status', ['pending_payment', 'submitted', 'approved', 'rejected', 'withdrawn'])->default('pending_payment');
    $table->timestamp('submitted_at')->useCurrent();
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('election_nominations');
    }
};
