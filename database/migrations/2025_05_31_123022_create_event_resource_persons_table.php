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
Schema::create('event_resource_persons', function (Blueprint $table) {
    $table->id();
    $table->foreignId('event_id')->constrained()->onDelete('cascade');
    $table->foreignId('resource_person_id')->constrained('resource_persons')->onDelete('cascade');
    $table->string('role_in_event')->nullable(); // e.g., Speaker, Panelist, Moderator
    $table->decimal('honorarium_amount', 10, 2)->nullable();
    $table->enum('invitation_status', ['pending', 'accepted', 'declined'])->default('pending');
    $table->timestamps();
    $table->unique(['event_id', 'resource_person_id']);
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_resource_persons');
    }
};
