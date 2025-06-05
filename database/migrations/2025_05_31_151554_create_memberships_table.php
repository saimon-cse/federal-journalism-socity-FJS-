<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {

         Schema::create('membership_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // e.g., Resource Person, General Member
            $table->text('description')->nullable();
            $table->string('slug')->unique(); // e.g., resource-person, general-member
            $table->boolean('is_active')->default(true);
            $table->decimal('monthly_amount', 10, 2)->nullable(); // Monthly fee
            $table->decimal('annual_amount', 10, 2)->nullable(); // Annual fee
            $table->boolean('is_recurring')->default(false); // Whether the fee is recurring
            $table->string('membership_duration')->nullable(); // e.g., 1 year, 6 months
            $table->timestamps();
        });

        Schema::create('memberships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
        $table->foreignId('membership_type_id')->constrained('membership_types')->cascadeOnDelete(); // e.g., 'annual', 'monthly', 'lifetime'
            $table->date('start_date')->nullable(); // Nullable for lifetime memberships
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
