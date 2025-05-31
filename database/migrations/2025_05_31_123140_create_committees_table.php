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
// database/migrations/xxxx_xx_xx_xxxxxx_create_committees_table.php
Schema::create('committees', function (Blueprint $table) {
    $table->id();
    $table->string('name'); // e.g., "Dhaka Divisional Committee 2024-2026"
    $table->foreignId('committee_type_id')->constrained('committee_types')->onDelete('cascade');
    $table->foreignId('division_id')->nullable()->constrained('divisions')->onDelete('set null'); // If divisional
    $table->foreignId('district_id')->nullable()->constrained('districts')->onDelete('set null'); // If district
    $table->foreignId('upazila_id')->nullable()->constrained('upazilas')->onDelete('set null'); // If upazila
    $table->date('formation_date')->nullable();
    $table->date('expiry_date')->nullable();
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
        Schema::dropIfExists('committees');
    }
};
