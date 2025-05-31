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
// database/migrations/xxxx_xx_xx_xxxxxx_create_elections_table.php
Schema::create('elections', function (Blueprint $table) {
    $table->id();
    $table->string('title'); // e.g., "District Committee Election - Dhaka 2024"
    $table->text('description')->nullable();
    $table->foreignId('committee_id')->nullable()->constrained('committees')->onDelete('set null'); // Election for which committee
    // Or target by location if not for a specific pre-existing committee
    $table->foreignId('division_id')->nullable()->constrained('divisions')->onDelete('set null');
    $table->foreignId('district_id')->nullable()->constrained('districts')->onDelete('set null');
    $table->foreignId('upazila_id')->nullable()->constrained('upazilas')->onDelete('set null');
    $table->timestamp('nomination_start_datetime');
    $table->timestamp('nomination_end_datetime')->nullable();
    $table->timestamp('withdrawal_end_datetime')->nullable();
    $table->timestamp('voting_start_datetime')->nullable();
    $table->timestamp('voting_end_datetime')->nullable();
    $table->timestamp('results_published_at')->nullable();
    $table->decimal('nomination_fee', 8, 2)->nullable();
    $table->enum('status', ['pending', 'nomination_open', 'nomination_closed', 'voting_open', 'voting_closed', 'completed', 'cancelled'])->default('pending');
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('elections');
    }
};
