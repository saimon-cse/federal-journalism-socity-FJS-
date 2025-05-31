<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->morphs('payable');
            $table->foreignId('payment_account_id_received_in')->nullable()->constrained('payment_accounts')->onDelete('set null');
            $table->decimal('amount', 10, 2);
            $table->string('external_transaction_id')->nullable();
            $table->string('payment_proof_path')->nullable();
            $table->timestamp('payment_date')->useCurrent();
            $table->string('status')->default('pending_verification');
            $table->foreignId('verified_by_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('verified_at')->nullable();
            $table->text('verification_notes')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('payments');
    }
};
