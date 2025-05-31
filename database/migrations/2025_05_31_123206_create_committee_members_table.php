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
// database/migrations/xxxx_xx_xx_xxxxxx_create_committee_members_table.php
Schema::create('committee_members', function (Blueprint $table) {
    $table->id();
    $table->foreignId('committee_id')->constrained()->onDelete('cascade');
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->string('position_in_committee'); // e.g., President, Secretary, Member
    // $table->foreignId('committee_position_id')->constrained('committee_positions'); // Or a separate positions table
    $table->date('start_date_in_position')->nullable();
    $table->date('end_date_in_position')->nullable();
    $table->boolean('is_manager')->default(false); // Is this user the committee manager?
    $table->timestamps();
    $table->unique(['committee_id', 'user_id']); // A user can be in a committee once with one position
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('committee_members');
    }
};
