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
// database/migrations/xxxx_xx_xx_xxxxxx_create_user_memberships_table.php
Schema::create('user_memberships', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->foreignId('membership_plan_id')->constrained('membership_plans')->onDelete('cascade');
    $table->foreignId('payment_id')->nullable()->constrained('payments')->onDelete('set null'); // Link to payment for this membership period
    $table->timestamp('start_date');
    $table->timestamp('end_date')->nullable(); // Nullable for lifetime or if renewed continuously
    $table->enum('status', ['active', 'expired', 'cancelled_by_user', 'cancelled_by_admin'])->default('active');
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_memberships');
    }
};
