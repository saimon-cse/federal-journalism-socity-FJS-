<?php
// create_divisions_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('divisions', function (Blueprint $table) {
            $table->id();
            $table->string('name_en')->unique();
            $table->string('name_bn')->unique();
            $table->string('slug')->unique(); // Unique slug for URL usage

            $table->timestamps();
        });
    }
    public function down() { Schema::dropIfExists('divisions'); }
};




return new class extends Migration {
    public function up() {
        Schema::create('districts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('division_id')->constrained('divisions')->cascadeOnDelete();
            $table->string('name_en')->unique();
            $table->string('name_bn')->unique();
            $table->string('slug')->unique(); // Unique slug for URL usage
            $table->timestamps();
        });
    }
    public function down() { Schema::dropIfExists('districts'); }
};


return new class extends Migration {
    public function up() {
        Schema::create('upazilas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('district_id')->constrained('districts')->cascadeOnDelete();
            $table->string('name_en')->unique();
            $table->string('name_bn')->unique();
            $table->string('slug')->unique(); // Unique slug for URL usage
            $table->timestamps();
        });
    }
    public function down() { Schema::dropIfExists('upazilas'); }
};









return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Full name for display
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            // Profile photo path/details will be handled by Spatie Media Library or a simple string path
            $table->string('profile_photo_path')->nullable();
            $table->boolean('is_active')->default(true); // To activate/deactivate users
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};



return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();

            // Personal Details
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('father_name')->nullable();
            $table->string('mother_name')->nullable();
            $table->date('dob')->nullable(); // Date of Birth
            $table->string('blood_group', 5)->nullable();
            $table->string('gender', 15)->nullable(); // e.g., male, female, other, prefer_not_to_say
            $table->string('religion')->nullable();

            // Contact Details
            $table->string('phone_primary')->nullable()->unique();
            $table->string('phone_secondary')->nullable()->unique();
            $table->string('whatsapp_number')->nullable()->unique();

            // Identity Documents (Numbers here, files via Spatie Media Library on User model)
            $table->string('nid_number')->nullable()->unique();
            $table->string('passport_number')->nullable()->unique();

            // Other Preferences
            $table->boolean('newsletter_subscribed')->default(false);
            $table->boolean('is_profile_public')->default(false); // Controls visibility of certain profile parts
            $table->string('workplace_type')->nullable();


            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_profiles');
    }
};



return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('address_type'); // e.g., 'permanent', 'current_residential', 'work'
            $table->foreignId('division_id')->nullable()->constrained('divisions')->nullOnDelete();
            $table->foreignId('district_id')->nullable()->constrained('districts')->nullOnDelete();
            $table->foreignId('upazila_id')->nullable()->constrained('upazilas')->nullOnDelete();
            $table->string('street_address')->nullable();
            $table->string('postal_code')->nullable();
            $table->text('address_details')->nullable(); // For any extra details
            $table->boolean('is_primary')->default(false); // If a user has multiple of one type, which is primary
            $table->timestamps();

            $table->index(['user_id', 'address_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_addresses');
    }
};

// database/migrations/xxxx_xx_xx_xxxxxx_create_user_educations_table.php


return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_educations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('degree_level'); // e.g., SSC, HSC, Bachelor's, Master's, PhD
            $table->string('degree_name'); // e.g., B.Sc. in CSE, M.A. in English
            $table->string('institution_name');
            $table->string('board_or_university')->nullable();
            $table->string('major_subject')->nullable();
            $table->year('passing_year')->nullable();
            $table->string('grade_or_cgpa')->nullable();
            $table->boolean('is_currently_studying')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_educations');
    }
};


// database/migrations/xxxx_xx_xx_xxxxxx_create_user_professional_experiences_table.php


return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_experiences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('organization_name');
            $table->string('designation'); // Position/Job Title
            $table->text('responsibilities')->nullable();
            $table->date('start_date');
            $table->date('end_date')->nullable(); // Null if currently working here
            $table->boolean('is_current_job')->default(false);
            $table->string('employment_type')->nullable(); // e.g., Full-time, Part-time, Contract
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_experiences');
    }
};


// database/migrations/xxxx_xx_xx_xxxxxx_create_user_social_media_links_table.php


return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_social_medias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('platform_name'); // e.g., Facebook, Twitter, LinkedIn, GitHub
            $table->string('profile_url')->unique(); // Or just username/handle if preferred
            $table->timestamps();

            $table->unique(['user_id', 'platform_name']); // A user has one link per platform
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_social_media_links');
    }
};







return new class extends Migration
{
    public function up()
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type');//resorse persoon, membership, training, event, etc.
            // e.g., 'resource_person', 'membership', 'training', 'event'
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['name', 'type']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('categories');
    }
};



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

// database/migrations/xxxx_xx_xx_xxxxxx_create_payment_accounts_table.php


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
    }
    public function down() { Schema::dropIfExists('payment_accounts'); }
};
// database/migrations/xxxx_xx_xx_xxxxxx_create_payment_methods_table.php


return new class extends Migration {
    public function up(): void
    {
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
    }
    public function down(): void { Schema::dropIfExists('payment_methods'); }
};

// database/migrations/xxxx_xx_xx_xxxxxx_create_payments_table.php


return new class extends Migration {
    public function up() {
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
    }
    public function down() { Schema::dropIfExists('payments'); }
};

// database/migrations/xxxx_xx_xx_xxxxxx_create_financial_transaction_categories_table.php

return new class extends Migration {
    public function up() {
        Schema::create('financial_transaction_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // Membership Fee, Training Fee, Event Sponsorship, Office Rent, Salaries, Bank Charges
            $table->string('type'); // 'income', 'expense'
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('parent_category_id')->nullable()->constrained('financial_transaction_categories')->nullOnDelete(); // For sub-categories
            $table->timestamps();
        });
    }
    public function down() { Schema::dropIfExists('financial_transaction_categories'); }
};

// database/migrations/xxxx_xx_xx_xxxxxx_create_financial_ledgers_table.php


return new class extends Migration {
    public function up() {
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
    }
    public function down() { Schema::dropIfExists('financial_ledgers'); }
};

// database/migrations/xxxx_xx_xx_xxxxxx_create_payment_gateway_logs_table.php


return new class extends Migration {
    public function up(): void
    {
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
    public function down(): void { Schema::dropIfExists('payment_gateway_logs'); }
};


// // create_memberships_table.php
// use Illuminate\Database\Migrations\Migration;
// use Illuminate\Database\Schema\Blueprint;
// use Illuminate\Support\Facades\Schema;





// // create_resource_persons_table.php
// use Illuminate\Database\Migrations\Migration;
// use Illuminate\Database\Schema\Blueprint;
// use Illuminate\Support\Facades\Schema;
// return new class extends Migration {
//     public function up() {
//         Schema::create('resource_persons', function (Blueprint $table) {
//             $table->id();
//             $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();
//             $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();
//             $table->text('bio')->nullable();
//             $table->string('expertise_areas')->nullable(); // Consider a pivot table resource_person_expertise for multiple areas
//             $table->boolean('is_visible')->default(false);
//             // CV and other files via Spatie Media Library
//             $table->string('cv_path')->nullable(); // Path to CV file

//             $table->boolean('is_approved')->default(false);
//             $table->foreignId('approved_by_user_id')->nullable()->constrained('users')->nullOnDelete();
//             $table->timestamp('approved_at')->nullable();
//             $table->unsignedInteger('total_trainings')->default(0);
//             $table->unsignedInteger('total_hours')->default(0);
//             $table->decimal('total_honorarium', 12, 2)->default(0.00);

//             $table->timestamps();
//         });
//     }
//     public function down() { Schema::dropIfExists('resource_persons'); }
// };



// database/migrations/xxxx_xx_xx_xxxxxx_create_committees_table.php


return new class extends Migration
{
    public function up(): void
    {
        Schema::create('committees', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->enum('level', ['central', 'division', 'district', 'upazila', 'other'])->default('other');
            $table->text('description')->nullable();
            $table->foreignId('division_id')->nullable()->constrained('divisions')->nullOnDelete();
            $table->foreignId('district_id')->nullable()->constrained('districts')->nullOnDelete();
            $table->foreignId('upazila_id')->nullable()->constrained('upazilas')->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->date('formation_date')->nullable(); // Date when the committee was established
            $table->date('term_start_date')->nullable();
            $table->date('term_end_date')->nullable();
            $table->foreignId('created_by_user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('committees');
    }
};

// database/migrations/xxxx_xx_xx_xxxxxx_create_committee_members_table.php (Pivot Table)


return new class extends Migration
{
    public function up(): void
    {


        Schema::create('committee_positions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // e.g., Chairperson, Secretary, Member
            $table->string('slug')->unique(); // Unique slug for URL usage
            $table->text('description')->nullable(); // Description of the position
            $table->boolean('is_active')->default(true); // If the position is currently active
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('committee_id')->constrained('committees')->cascadeOnDelete(); // Reference to the committee this position belongs to
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });


        Schema::create('committee_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('committee_id')->constrained('committees')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('position_id')->nullable()->constrained('committee_positions')->nullOnDelete()->comment('Reference to committee_positions table');
            $table->boolean('is_manager')->default(false)->comment('If this member is the designated Committee Manager for this committee');
            $table->date('appointed_on')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('status')->default('active'); // e.g., active, inactive, resigned, removed
            $table->timestamps();

            $table->unique(['committee_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('committee_members');
    }
};


// database/migrations/xxxx_xx_xx_xxxxxx_create_elections_table.php


return new class extends Migration
{
    public function up(): void
    {
        Schema::create('elections', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->date('election_date')->nullable();
            $table->timestamp('nomination_start_datetime');
            $table->timestamp('nomination_end_datetime');
            $table->timestamp('withdrawal_end_datetime')->nullable();
            $table->timestamp('results_declared_at')->nullable();

            $table->foreignId('committee_id')->nullable()->comment('Election for a specific committee')->constrained('committees')->nullOnDelete();
            // OR broader geographic targeting if not committee-specific
            $table->enum('level', ['central', 'division', 'district', 'upazila', 'other'])->nullable();
            $table->foreignId('target_division_id')->nullable()->constrained('divisions')->nullOnDelete();
            $table->foreignId('target_district_id')->nullable()->constrained('districts')->nullOnDelete();
            $table->foreignId('target_upazila_id')->nullable()->constrained('upazilas')->nullOnDelete();

            $table->decimal('nomination_fee', 10, 2)->default(0.00);
            $table->foreignId('default_payment_account_id')->nullable()->comment('For nomination fees')->constrained('payment_accounts')->nullOnDelete();
            $table->enum('status', ['pending_setup', 'announced', 'nominations_open', 'nominations_closed', 'scrutiny', 'withdrawal_period', 'final_candidates', 'voting_open', 'voting_closed', 'results_declared', 'completed', 'cancelled'])->default('pending_setup');
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('elections');
    }
};

// database/migrations/xxxx_xx_xx_xxxxxx_create_election_positions_table.php


return new class extends Migration
{
    public function up(): void
    {
        Schema::create('election_positions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('election_id')->constrained('elections')->cascadeOnDelete();
            // $table->string('name')->comment('e.g., President, General Secretary');
            $table->foreignId('position_id')->nullable()->constrained('committee_positions')->nullOnDelete(); // If this position is linked to a committee position
            $table->integer('number_of_seats')->default(1);
            $table->text('description')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['election_id', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('election_positions');
    }
};

// database/migrations/xxxx_xx_xx_xxxxxx_create_election_nominations_table.php


return new class extends Migration
{
    public function up(): void
    {
        Schema::create('election_nominations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('election_position_id')->constrained('election_positions')->cascadeOnDelete();
            $table->foreignId('user_id')->comment('Candidate User ID')->constrained('users')->cascadeOnDelete();
            $table->timestamp('application_datetime')->useCurrent();
            $table->enum('status', ['pending_payment', 'pending_approval', 'approved', 'rejected', 'withdrawn'])->default('pending_payment');
            $table->foreignId('payment_id')->nullable()->constrained('payments')->nullOnDelete();
            $table->foreignId('processed_by')->nullable()->comment('Admin who approved/rejected')->constrained('users')->nullOnDelete();
            $table->timestamp('processed_at')->nullable();
            $table->text('processing_remarks')->nullable()->comment('Reason for approval/rejection');
            $table->timestamp('withdrawn_at')->nullable();
            $table->text('withdrawal_reason')->nullable();
            $table->timestamps();

            $table->unique(['election_position_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('election_nominations');
    }
};



// database/migrations/xxxx_xx_xx_xxxxxx_create_allowance_types_table.php
// use Illuminate\Database\Migrations\Migration;
// use Illuminate\Database\Schema\Blueprint;
// use Illuminate\Support\Facades\Schema;
// return new class extends Migration {
//     public function up() {
//         Schema::create('allowance_types', function (Blueprint $table) {
//             $table->id();
//             $table->string('name')->unique(); // শিক্ষাবৃত্তি, চিকিৎসা অনুদান, Disaster Relief
//             $table->string('slug')->unique();
//             $table->text('description')->nullable();
//             $table->text('eligibility_criteria')->nullable();
//             $table->decimal('max_amount_per_application', 10, 2)->nullable();
//             $table->decimal('min_amount_per_application', 10, 2)->nullable();

//             $table->boolean('requires_documents')->default(true);
//             $table->text('required_documents_list')->nullable(); // e.g., "NID Copy, Medical Certificate, Student ID"
//             $table->boolean('is_active')->default(true);
//             $table->timestamps();
//         });
//     }
//     public function down() { Schema::dropIfExists('allowance_types'); }
// };

// // database/migrations/xxxx_xx_xx_xxxxxx_create_allowance_applications_table.php
// use Illuminate\Database\Migrations\Migration;
// use Illuminate\Database\Schema\Blueprint;
// use Illuminate\Support\Facades\Schema;
// return new class extends Migration {
//     public function up() {
//         Schema::create('allowance_applications', function (Blueprint $table) {
//             $table->id();
//             $table->string('application_uuid')->unique();
//             $table->foreignId('user_id')->comment('Member applying')->constrained('users')->cascadeOnDelete();
//             $table->foreignId('allowance_type_id')->constrained('allowance_types')->cascadeOnDelete();
//             $table->text('application_reason_details');
//             $table->decimal('requested_amount', 10, 2)->nullable();
//             $table->string('status')->default('submitted'); // submitted, under_review, pending_documents, approved, partially_approved, payment_processing, disbursed, rejected, withdrawn
//             // Supporting documents via Spatie Media Library on this model
//             $table->foreignId('reviewed_by_user_id')->nullable()->constrained('users')->nullOnDelete();
//             $table->timestamp('reviewed_at')->nullable();
//             $table->text('reviewer_remarks')->nullable();
//             $table->foreignId('approved_by_user_id')->nullable()->constrained('users')->nullOnDelete(); // Project/Allowance Manager
//             $table->timestamp('approved_at')->nullable();
//             $table->decimal('approved_amount', 10, 2)->nullable();
//             $table->text('approver_remarks')->nullable();
//             $table->timestamp('disbursed_at')->nullable();
//             $table->foreignId('disbursed_by_user_id')->nullable()->constrained('users')->nullOnDelete(); // Finance role
//             $table->foreignId('disbursement_payment_account_id')->nullable()->constrained('payment_accounts')->nullOnDelete();
//             $table->string('disbursement_transaction_ref')->nullable();
//             $table->text('applicant_withdrawal_reason')->nullable();
//             $table->timestamps();
//         });
//     }
//     public function down() { Schema::dropIfExists('allowance_applications'); }
// };


//--------------------------------------




return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Allowance Types Table
        Schema::create('allowance_types', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // চিকিৎসা অনুদান, শিক্ষাবৃত্তি/শিক্ষা সহায়তা
            $table->string('name_en')->nullable(); // English name
            $table->text('description')->nullable();
            $table->text('description_en')->nullable();
            $table->string('slug')->unique();
            $table->decimal('max_amount', 12, 2)->nullable(); // Maximum allowance amount
            $table->decimal('min_amount', 12, 2)->nullable(); // Minimum allowance amount
            $table->integer('max_applications_per_year')->default(1); // How many times per year a member can apply
            $table->boolean('is_active')->default(true);
            $table->text('required_documents')->nullable(); // JSON array of required document types
            $table->text('eligibility_criteria')->nullable(); // JSON array of criteria
            $table->integer('processing_days')->default(30); // Expected processing time
            $table->text('terms_and_conditions')->nullable();
            $table->timestamps();
        });

        // 2. Allowance Applications Table
        Schema::create('allowance_applications', function (Blueprint $table) {
            $table->id();
            $table->string('application_number')->unique(); // Auto-generated unique number
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('allowance_type_id')->constrained('allowance_types')->restrictOnDelete();
            $table->decimal('applied_amount', 12, 2);
            $table->decimal('approved_amount', 12, 2)->nullable();
            $table->decimal('disbursed_amount', 12, 2)->nullable();
            $table->string('currency_code', 3)->default('BDT'); // Currency code, default to BDT

            $table->text('reason'); // Why they need this allowance
            $table->text('additional_details')->nullable();
            $table->enum('status', [
                'draft',
                'submitted',
                'under_review',
                'approved',
                'rejected',
                'payment_processed',
                'completed'
            ])->default('draft');
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');

            // Review process
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('review_notes')->nullable();
            $table->text('rejection_reason')->nullable();

            // Payment process
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->date('payment_date')->nullable();
            $table->text('payment_notes')->nullable();

            // Tracking
            $table->date('application_date');
            $table->date('expected_completion_date')->nullable();
            // Remove status_history JSON column.
            // Status changes should be tracked in a separate log table (allowance_application_logs).
            $table->timestamps();

            // Index for better performance
            $table->index(['user_id', 'allowance_type_id', 'status']);
        });

        // 3. Application Documents Table
        Schema::create('allowance_application_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained('allowance_applications')->onDelete('cascade');
            $table->string('document_type'); // medical_report, education_certificate, income_certificate, etc.
            $table->string('document_name'); // Original filename
            $table->string('file_path'); // Storage path
            // $table->string('file_type'); // pdf, jpg, png, etc.
            // $table->integer('file_size'); // File size in bytes
            $table->text('description')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->text('verification_notes')->nullable();
            $table->timestamps();
        });

        // 4. Allowance Payments Table (Links to financial_transactions)
         Schema::create('allowance_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained('allowance_applications')->restrictOnDelete();

            // MODIFICATION HERE: Define column without immediate constraint
            $table->forignId('payment_id')->constrained('payments')->cascadeOnDelete(); // Link to the payment record in payments table
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

        // 5. Application Reviews Table (For detailed review process)
        Schema::create('allowance_application_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained('allowance_applications')->onDelete('cascade');
            $table->foreignId('reviewer_id')->constrained('users')->restrictOnDelete();
            $table->enum('review_type', ['initial', 'document_verification', 'financial', 'final'])->default('initial');
            $table->enum('recommendation', ['approve', 'reject', 'request_more_info', 'pending']);
            $table->text('comments')->nullable();
            $table->text('checklist_notes')->nullable(); // Store checklist notes as plain text
            $table->timestamp('reviewed_at');
            $table->timestamps();
        });

        // 6. Document Types Master Table (Optional - for standardization)
        Schema::create('document_types', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Medical Certificate, Educational Certificate, etc.
            // $table->string('name_bn')->nullable(); // Bengali name
            // $table->string('code')->unique(); // medical_cert, edu_cert, etc.
            $table->text('description')->nullable();
            $table->string('allowed_file_types')->nullable(); // Comma-separated: pdf,jpg,png
            $table->integer('max_file_size')->default(5120); // KB
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 7. Application Status Log Table (For audit trail)
        Schema::create('allowance_application_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained('allowance_applications')->onDelete('cascade');
            $table->string('previous_status')->nullable();
            $table->string('new_status')->nullable();
            $table->string('action_taken'); // e.g., 'submitted', 'approved', 'rejected', 'payment_processed'
            $table->foreignId('changed_by')->constrained('users')->restrictOnDelete();
            $table->text('reason')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('changed_at');
            $table->timestamps();
        });

        // 8. Member Allowance History Summary (For quick queries)
        // Schema::create('member_allowance_summaries', function (Blueprint $table) {
        //     $table->id();
        //     $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
        //     $table->foreignId('allowance_type_id')->constrained('allowance_types')->restrictOnDelete();
        //     $table->integer('total_applications')->default(0);
        //     $table->integer('approved_applications')->default(0);
        //     $table->integer('rejected_applications')->default(0);
        //     $table->decimal('total_amount_received', 12, 2)->default(0);
        //     $table->integer('current_year_applications')->default(0);
        //     $table->date('last_application_date')->nullable();
        //     $table->date('last_payment_date')->nullable();
        //     $table->timestamps();

        //     // Unique constraint
        //     $table->unique(['user_id', 'allowance_type_id']);
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

        Schema::dropIfExists('allowance_application_status_logs');
        Schema::dropIfExists('document_types');
        Schema::dropIfExists('allowance_application_reviews');
        Schema::dropIfExists('allowance_payments');
        Schema::dropIfExists('allowance_application_documents');
        Schema::dropIfExists('allowance_applications');
        Schema::dropIfExists('allowance_types');
    }
};


// database/migrations/xxxx_xx_xx_xxxxxx_create_announcements_table.php


return new class extends Migration
{
    public function up(): void
    {
        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('content');
            $table->foreignId('posted_by')->constrained('users')->cascadeOnDelete();
            $table->timestamp('publish_at')->nullable()->useCurrent();
            $table->timestamp('expires_at')->nullable();

            $table->enum('target_audience_type', ['all_users', 'roles', 'members', 'non_members', 'divisions', 'districts', 'upazilas', 'specific_users'])->default('all_users');
            $table->json('target_role_ids')->nullable()->comment('Array of role IDs from Spatie roles table');
            $table->json('target_division_ids')->nullable()->comment('Array of division_ids');
            $table->json('target_district_ids')->nullable()->comment('Array of district_ids');
            $table->json('target_upazila_ids')->nullable()->comment('Array of upazila_ids');
            $table->json('target_user_ids')->nullable()->comment('Array of user_ids');

            $table->boolean('send_email_notification')->default(false);
            $table->boolean('show_on_dashboard')->default(true);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('announcements');
    }
};


// database/migrations/xxxx_xx_xx_xxxxxx_create_complaints_suggestions_table.php


return new class extends Migration
{
    public function up(): void
    {
        Schema::create('complaints_suggestions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->comment('User who submitted, if logged in and not anonymous')->constrained('users')->nullOnDelete();
            $table->string('submitted_by_name')->nullable()->comment('Name if anonymous or for record keeping');
            $table->string('submitted_by_email')->nullable()->comment('Email if anonymous or for record keeping');
            $table->enum('type', ['complaint', 'suggestion', 'feedback']);
            $table->string('subject');
            $table->text('description');
            $table->boolean('is_anonymous')->default(false);
            $table->enum('status', ['new', 'opened', 'under_review', 'awaiting_reply', 'resolved', 'closed', 'rejected'])->default('new');
            $table->text('admin_remarks')->nullable();
            $table->foreignId('resolved_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('resolved_at')->nullable();
            // Attachments should use Spatie Media Library on this model
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('complaints_suggestions');
    }
};
// database/migrations/xxxx_xx_xx_xxxxxx_create_complaint_suggestion_replies_table.php

return new class extends Migration {
    public function up() {
        Schema::create('complaint_suggestion_replies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('complaint_id')->constrained('complaint_suggestions')->cascadeOnDelete();
            $table->foreignId('user_id')->comment('User who replied (can be submitter or agent)')->constrained('users')->cascadeOnDelete();
            $table->longText('reply_content');
            $table->string('reply_type')->default('agent'); // agent, user
            // Attachments for reply via Spatie Media Library
            // $table->string('attachment_path')->nullable(); // Path to any file attached with the reply

            $table->timestamps();
        });
    }
    public function down() { Schema::dropIfExists('complaint_suggestion_replies'); }
};



// database/migrations/xxxx_xx_xx_xxxxxx_create_job_postings_table.php

return new class extends Migration {
    public function up() {
        Schema::create('job_postings', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->longText('description');
            $table->string('banner_image_path')->nullable(); // Or Spatie
            $table->date('application_deadline');
            $table->string('apply_instructions')->nullable();
            $table->foreignId('posted_by')->constrained('users')->cascadeOnDelete();
            $table->string('status')->default('draft'); // draft, published, interviewing, filled, closed, expired
            $table->timestamps();
        });
    }
    public function down() { Schema::dropIfExists('job_postings'); }
};

// database/migrations/xxxx_xx_xx_xxxxxx_create_job_applications_table.php

return new class extends Migration {
    public function up() {
        Schema::create('job_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_id')->constrained('job_postings')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            // CV and NID files via Spatie Media Library on this model or linked to User
            $table->text('cv')->nullable();
            $table->string('status')->default('submitted'); // submitted, viewed, under_review, shortlisted, interviewed, offered, hired, rejected, withdrawn
            $table->decimal('expected_salary', 10, 2)->nullable();
            $table->date('available_start_date')->nullable();
            $table->text('notes_for_hiring_manager')->nullable(); // Internal notes by HR
            $table->timestamp('applied_at')->useCurrent();
            $table->timestamps();
            $table->unique(['job_posting_id', 'user_id']);
        });
    }
    public function down() { Schema::dropIfExists('job_applications'); }
};
