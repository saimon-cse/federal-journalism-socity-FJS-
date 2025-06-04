<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('memberships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('membership_type'); // e.g., 'annual', 'monthly', 'lifetime'
            $table->date('start_date');
            $table->date('end_date')->nullable(); // Nullable for lifetime
            $table->string('status')->default('pending_payment'); // pending_payment, active, expired, cancelled, grap
            $table->timestamp('last_payment_date')->nullable(); // When payment was made
            $table->timestamp('next_due_date')->nullable(); // For recurring memberships
            $table->foreignId('approved_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });
    }
    public function down() { Schema::dropIfExists('memberships'); }
};
