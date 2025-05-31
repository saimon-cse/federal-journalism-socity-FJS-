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

Schema::create('election_votes', function (Blueprint $table) {
    $table->id();
    $table->foreignId('election_id')->constrained()->onDelete('cascade');
    $table->foreignId('election_position_id')->constrained('election_positions')->onDelete('cascade');
    $table->foreignId('voter_user_id')->constrained('users')->onDelete('cascade'); // Voter
    $table->foreignId('candidate_user_id')->constrained('users')->onDelete('cascade'); // Voted for which candidate
    // Or $table->foreignId('nomination_id')->constrained('election_nominations')->onDelete('cascade');
    $table->timestamp('voted_at')->useCurrent();
    $table->ipAddress('ip_address')->nullable();
    $table->timestamps();
    $table->unique(['election_position_id', 'voter_user_id']); // A voter can vote once for a position
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('election_votes');
    }
};
