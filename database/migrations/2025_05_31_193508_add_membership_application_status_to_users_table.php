<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('membership_application_status')->nullable()->after('user_type'); // e.g., pending_payment, pending_approval, approved, rejected
            $table->text('membership_rejection_reason')->nullable()->after('membership_application_status');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['membership_application_status', 'membership_rejection_reason']);
        });
    }
};
