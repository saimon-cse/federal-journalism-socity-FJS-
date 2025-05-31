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
Schema::create('membership_plans', function (Blueprint $table) {
    $table->id();
    $table->string('name')->unique(); // e.g., Monthly, Yearly, Lifetime
    $table->decimal('fee', 8, 2);
    $table->string('duration_in_days_or_months'); // e.g., 30, 365, or 'lifetime'
    $table->text('description')->nullable();
    $table->boolean('is_active')->default(true);
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('membership_plans');
    }
};
