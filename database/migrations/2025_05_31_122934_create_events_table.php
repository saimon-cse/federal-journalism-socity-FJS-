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
// database/migrations/xxxx_xx_xx_xxxxxx_create_events_table.php
Schema::create('events', function (Blueprint $table) {
    $table->id();
    $table->string('title');
    $table->text('description')->nullable();
    $table->string('event_code')->unique()->nullable();
    $table->enum('type', ['online', 'offline', 'hybrid'])->default('offline');
    $table->string('venue_name')->nullable();
    $table->text('venue_address')->nullable();
    $table->timestamp('start_datetime');
    $table->timestamp('end_datetime')->nullable();
    $table->enum('payment_type', ['paid', 'free'])->default('free');
    $table->decimal('fee', 10, 2)->nullable();
    $table->integer('max_attendees')->nullable();
    $table->string('banner_image')->nullable();
    $table->string('youtube_link')->nullable();
    $table->enum('status', ['upcoming', 'ongoing', 'completed', 'postponed', 'cancelled'])->default('upcoming');
    $table->boolean('is_published')->default(false);
    $table->timestamps();
    $table->softDeletes();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
