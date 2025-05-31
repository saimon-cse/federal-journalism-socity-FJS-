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
 // database/migrations/xxxx_xx_xx_xxxxxx_create_complaints_suggestions_table.php
Schema::create('complaints_suggestions', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null'); // User who submitted, nullable if anonymous
    $table->string('subject');
    $table->text('message');
    $table->enum('type', ['complaint', 'suggestion']);
    $table->boolean('is_anonymous')->default(false);
    $table->enum('status', ['new', 'seen', 'in_progress', 'resolved', 'closed'])->default('new');
    $table->foreignId('replied_by_user_id')->nullable()->constrained('users')->onDelete('set null'); // Admin who replied
    $table->text('reply_message')->nullable();
    $table->timestamp('replied_at')->nullable();
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('complaints_suggestions');
    }
};
