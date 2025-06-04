<?php
// database/migrations/xxxx_xx_xx_xxxxxx_create_payment_accounts_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('payment_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('account_name')->unique(); // e.g., "Org Main Bkash", "Event Collection DBBL"
            $table->string('account_provider'); // e.g., Bkash, Nagad, DBBL, SSLCommerz (for virtual accounts)
            $table->string('account_type'); // e.g., Mobile Financial Service, Bank Account, Payment Gateway Wallet
            $table->string('account_identifier')->nullable(); // Bkash number, Bank Account Number
            $table->string('account_holder_name')->nullable();
            $table->string('bank_name')->nullable(); // If bank account
            $table->string('branch_name')->nullable(); // If bank account
            $table->string('routing_number')->nullable(); // If bank account
            // $table->text('api_credentials_encrypted')->nullable(); // For gateway integrations (ALWAYS ENCRYPT)
            $table->boolean('is_active')->default(true);
            $table->boolean('allow_user_manual_payment_to')->default(false); // Can users be instructed to pay directly to this?
            $table->text('manual_payment_instructions')->nullable(); // If manual payments are allowed
            // $table->string('currency_code', 3)->default('BDT');
            // Optional: Real-time balance tracking. Requires careful atomic updates.
            // $table->decimal('current_balance', 15, 2)->default(0.00);
            $table->timestamps();
        });


        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., "Bkash (Manual)", "Nagad (API)", "Bank Transfer", "SSLCommerz (Card/MFS)"
            $table->string('method_key')->unique(); // e.g., bkash_manual, nagad_api, bank_transfer_manual, sslcommerz_gateway
            $table->string('type'); // 'manual', 'gateway'
            $table->string('provider_name')->nullable(); // e.g., Bkash, Nagad, SSLCommerz
            $table->text('description')->nullable(); // Instructions or details
            $table->string('logo_path')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
        //    // If this method directs to a specific org account for manual payments
            $table->foreignId('default_manual_account_id')->nullable()->constrained('payment_accounts')->nullOnDelete();
            $table->timestamps();
        });


        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('payment_uuid')->unique(); // For external reference/tracking

            // Payer Information
            $table->foreignId('user_id')->nullable()->comment('Paying system user, if any')->constrained('users')->nullOnDelete();
            $table->string('payer_name')->nullable(); // If payer is not a system user or for overriding
            $table->string('payer_email')->nullable();
            $table->string('payer_phone')->nullable();

            // What is being paid for
            $table->morphs('payable'); // e.g., Membership, TrainingRegistration

            // Amount Details
            $table->decimal('amount_due', 12, 2);
            $table->decimal('amount_paid', 12, 2)->default(0.00); // Actual amount confirmed as paid
            $table->string('currency_code', 3)->default('BDT');
            $table->decimal('discount_amount', 12, 2)->default(0.00);
            $table->decimal('vat_tax_amount', 12, 2)->default(0.00);
            $table->decimal('net_amount_payable', 12, 2); // amount_due - discount + vat_tax

            // Payment Method and Details (Chosen/Used by Payer)
            $table->foreignId('payment_method_id')->nullable()->constrained('payment_methods')->nullOnDelete();

            // Manual Payment Specifics (if payment_method.type == 'manual')
            $table->string('manual_transaction_id_user')->nullable()->comment('User-provided TrxID');
            $table->timestamp('manual_payment_datetime_user')->nullable()->comment('User-stated payment date & time');
            $table->foreignId('manual_payment_to_account_id')->nullable()->comment('Org account user claims to have paid to')->constrained('payment_accounts')->nullOnDelete();
            // Payment proof (screenshot) via Spatie Media Library on this model

            // Gateway Payment Specifics (if payment_method.type == 'gateway')
            $table->string('gateway_name')->nullable(); // e.g., 'sslcommerz', 'bkash_pgw'
            $table->string('gateway_transaction_id')->nullable()->unique(); // ID from payment gateway for this payment attempt
            $table->text('gateway_checkout_url')->nullable(); // URL for user to complete payment
            $table->timestamp('gateway_initiated_at')->nullable();
            $table->timestamp('gateway_response_at')->nullable();

            // Status and Verification
            $table->string('status');
            // Common statuses:
            // - pending_user_action (e.g., needs to go to gateway URL)
            // - pending_manual_verification
            // - processing_gateway
            // - successful (verified money received)
            // - failed_gateway
            // - failed_verification (manual payment proof invalid)
            // - expired (e.g. payment link expired)
            // - cancelled
            // - refunded, partially_refunded

            $table->foreignId('verified_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable(); // When money is confirmed in org account
            $table->text('verification_remarks')->nullable();

            $table->text('notes')->nullable(); // General notes for this payment record
            $table->timestamps();

            $table->index(['payable_type', 'payable_id']);
            $table->index('status');
        });




        Schema::create('financial_transaction_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // Membership Fee, Training Fee, Event Sponsorship, Office Rent, Salaries, Bank Charges
            $table->string('type'); // 'income', 'expense'
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('parent_category_id')->nullable()->constrained('financial_transaction_categories')->nullOnDelete(); // For sub-categories
            $table->timestamps();
        });




        Schema::create('financial_ledgers', function (Blueprint $table) {
            $table->id();
            $table->string('ledger_entry_uuid')->unique();
            $table->timestamp('transaction_datetime')->useCurrent(); // When the transaction is recorded/occurred

            $table->string('entry_type');
            // 'income': Money coming INTO an org account.
            // 'expense': Money going OUT FROM an org account.
            // 'transfer': Money moving BETWEEN two org accounts.
            // 'opening_balance': Initial balance for an account.
            // 'reconciliation_adjustment': Adjustments after bank reconciliation.

            $table->decimal('amount', 15, 2);
            $table->string('currency_code', 3)->default('BDT');
            $table->text('description'); // Clear description of the transaction

            $table->foreignId('category_id')->nullable()->comment('For income/expense types')->constrained('financial_transaction_categories')->nullOnDelete();

            // Source and Destination of Funds (within organization's accounts)
            // For 'income': `to_payment_account_id` is the org account receiving money. `from_payment_account_id` is null or conceptual external.
            // For 'expense': `from_payment_account_id` is the org account money is leaving. `to_payment_account_id` is null or conceptual external.
            // For 'transfer': Both `from_payment_account_id` and `to_payment_account_id` are org accounts.
            $table->foreignId('from_payment_account_id')->nullable()->comment('Org account money came FROM (for expense/transfer)')->constrained('payment_accounts')->nullOnDelete();
            $table->foreignId('to_payment_account_id')->nullable()->comment('Org account money went TO (for income/transfer)')->constrained('payment_accounts')->nullOnDelete();

            // Link to originating application-level records
            $table->foreignId('payment_id')->nullable()->comment('Originating Payment record from users module')->constrained('payments')->nullOnDelete();
            $table->morphs('referenceable')->nullable(); // Optional: Link to Event, Training, AllowanceApplication etc.

            // External Party Details (if applicable, e.g., for an expense to a vendor)
            $table->string('external_party_name')->nullable();
            $table->string('external_reference_id')->nullable(); // Invoice number, bank statement transaction ID

            $table->foreignId('recorded_by_user_id')->constrained('users')->cascadeOnDelete(); // User who entered this ledger item
            $table->text('internal_notes')->nullable();

            // Reconciliation fields
            $table->boolean('is_reconciled')->default(false);
            $table->timestamp('reconciled_at')->nullable();
            $table->foreignId('reconciled_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('bank_statement_line_id')->nullable(); // If linking to imported bank statement lines

            $table->timestamps(); // Record creation/update time in this table

            $table->index('entry_type');
            $table->index('transaction_datetime');
        });


        Schema::create('payment_gateway_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_id')->nullable()->constrained('payments')->nullOnDelete(); // Link to the payment attempt
            $table->string('gateway_name'); // e.g., sslcommerz, bkash_pgw
            $table->string('log_type'); // request, response, webhook_notification, error
            $table->string('direction'); // outgoing, incoming
            $table->string('url_endpoint')->nullable();
            $table->text('request_headers')->nullable();
            $table->longText('request_payload')->nullable();
            $table->integer('response_status_code')->nullable();
            $table->text('response_headers')->nullable();
            $table->longText('response_payload')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();
        });



    }
    public function down() {
        Schema::dropIfExists('financial_transaction_categories');
        Schema::dropIfExists('financial_ledgers');
        Schema::dropIfExists('payment_gateway_logs');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('payment_methods');
        Schema::dropIfExists('payment_accounts');
    }
};
