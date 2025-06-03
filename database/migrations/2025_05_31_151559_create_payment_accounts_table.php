<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('account_name'); // e.g., "Org Bkash Personal", "Org DBBL Account"
            $table->string('account_type'); // Enum: 'bkash', 'nagad', 'rocket', 'bank_account', 'other'
            $table->string('account_identifier'); // Bkash/Nagad number, Bank Account Number
            $table->string('account_holder_name')->nullable();
            $table->string('bank_name')->nullable(); // For bank accounts
            $table->string('branch_name')->nullable(); // For bank accounts
            $table->string('routing_number')->nullable(); // For bank accounts
            $table->text('instructions_for_payer')->nullable(); // e.g., "Use reference: Membership Fee"
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_accounts');
    }
};
