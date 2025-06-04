<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Allowance Types Table (MODIFIED)
        Schema::create('allowance_types', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // চিকিৎসা অনুদান, শিক্ষাবৃত্তি/শিক্ষা সহায়তা
            $table->text('description')->nullable();
            $table->string('slug')->unique();
            $table->decimal('max_amount', 12, 2)->nullable();
            $table->decimal('min_amount', 12, 2)->nullable();
            $table->integer('max_applications_per_year')->default(1);
            $table->boolean('is_active')->default(true);
            // $table->text('required_documents')->nullable(); // REMOVED JSON/TEXT field
            $table->text('details')->nullable();
            $table->integer('processing_days')->default(30);
            $table->text('terms_and_conditions')->nullable();
            $table->timestamps();
        });

          // 6. Document Types Master Table (Ensure this is created before allowance_type_document_type)
        // This should be in its own migration file if not already.
        if (!Schema::hasTable('document_types')) {
            Schema::create('document_types', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->text('description')->nullable();
                $table->string('allowed_file_types')->nullable();
                $table->integer('max_file_size')->default(5120); // KB
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // NEW Pivot Table: allowance_type_document_type
        Schema::create('allowance_document_type', function (Blueprint $table) {
            $table->id();
            $table->foreignId('allowance_type_id')->constrained('allowance_types')->cascadeOnDelete();
            $table->foreignId('document_type_id')->constrained('document_types')->cascadeOnDelete(); // Assumes document_types table exists
            $table->timestamps();

            $table->unique(['allowance_type_id', 'document_type_id'], 'allowance_type_document_type_unique');
        });

        // 2. Allowance Applications Table (Keep as it was or in its separate migration)
        Schema::create('allowance_applications', function (Blueprint $table) {
            $table->id();
            $table->string('application_number')->unique();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('allowance_type_id')->constrained('allowance_types')->restrictOnDelete();
            $table->decimal('applied_amount', 12, 2);
            $table->decimal('approved_amount', 12, 2)->nullable();
            $table->decimal('disbursed_amount', 12, 2)->nullable();
            $table->string('currency_code', 3)->default('BDT');
            $table->text('reason');
            $table->text('additional_details')->nullable();
            $table->enum('status', [
                'draft', 'submitted', 'under_review', 'approved', 'rejected', 'payment_processed', 'completed'
            ])->default('draft');
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('review_notes')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->date('payment_date')->nullable();
            $table->text('payment_notes')->nullable();
            $table->date('application_date');
            $table->date('expected_completion_date')->nullable();
            $table->timestamps();
            $table->index(['user_id', 'allowance_type_id', 'status']);
        });

        // 3. Application Documents Table (Keep as it was or in its separate migration)
        Schema::create('allowance_application_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained('allowance_applications')->onDelete('cascade');
            $table->string('document_name'); // Original filename
            $table->string('file_path'); // Storage path
            // Link to document_types table if you want to categorize uploaded docs
            $table->foreignId('document_type_id')->nullable()->constrained('document_types')->nullOnDelete();
            $table->text('description')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->text('verification_notes')->nullable();
            $table->timestamps();
        });

        // 4. Allowance Payments Table (Keep as it was or in its separate migration)
         Schema::create('allowance_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained('allowance_applications')->restrictOnDelete();
            $table->foreignId('payment_id')->constrained('payments')->cascadeOnDelete();
            $table->string('payment_uuid')->unique();
            $table->decimal('amount', 12, 2);
            $table->enum('payment_method', ['bank_transfer', 'cash', 'mobile_banking', 'cheque'])->default('bank_transfer');
            $table->string('payment_reference')->nullable();
            $table->text('payment_details')->nullable();
            $table->enum('status', ['pending', 'processing', 'completed', 'failed', 'cancelled'])->default('pending');
            $table->date('payment_date');
            $table->foreignId('processed_by')->constrained('users')->restrictOnDelete();
            $table->text('notes')->nullable();
            $table->string('receipt_path')->nullable();
            $table->timestamps();
        });

        // 5. Application Reviews Table (Keep as it was or in its separate migration)
        Schema::create('allowance_application_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained('allowance_applications')->onDelete('cascade');
            $table->foreignId('reviewer_id')->constrained('users')->restrictOnDelete();
            $table->enum('review_type', ['initial', 'document_verification', 'financial', 'final'])->default('initial');
            $table->enum('recommendation', ['approve', 'reject', 'request_more_info', 'pending']);
            $table->text('comments')->nullable();
            $table->text('checklist_notes')->nullable();
            $table->timestamp('reviewed_at');
            $table->timestamps();
        });




        // 7. Application Status Log Table (Keep as it was or in its separate migration)
        Schema::create('allowance_application_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained('allowance_applications')->onDelete('cascade');
            $table->string('previous_status')->nullable();
            $table->string('new_status')->nullable();
            $table->string('action_taken');
            $table->foreignId('changed_by')->constrained('users')->restrictOnDelete();
            $table->text('reason')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('changed_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('allowance_application_logs');
        // Schema::dropIfExists('document_types'); // Be cautious if other tables depend on it
        Schema::dropIfExists('allowance_application_reviews');
        Schema::dropIfExists('allowance_payments');
        Schema::dropIfExists('allowance_application_documents');
        Schema::dropIfExists('allowance_applications');
        Schema::dropIfExists('allowance_type_document_type'); // Drop pivot table
        Schema::dropIfExists('allowance_types');
    }
};
