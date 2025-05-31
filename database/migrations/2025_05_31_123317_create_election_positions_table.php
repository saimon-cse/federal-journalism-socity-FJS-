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
// database/migrations/xxxx_xx_xx_xxxxxx_create_election_positions_table.php
Schema::create('election_positions', function (Blueprint $table) {
    $table->id();
    $table->foreignId('election_id')->constrained()->onDelete('cascade');
    $table->string('position_name'); // সভাপতি, সাধারণ সম্পাদক
    $table->integer('number_of_seats')->default(1);
    $table->timestamps();
    $table->unique(['election_id', 'position_name']);
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('election_positions');
    }
};
