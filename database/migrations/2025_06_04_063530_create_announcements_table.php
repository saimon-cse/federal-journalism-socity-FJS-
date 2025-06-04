<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
            // REMOVED JSON COLUMNS:
            // $table->json('target_role_ids')->nullable();
            // $table->json('target_division_ids')->nullable();
            // $table->json('target_district_ids')->nullable();
            // $table->json('target_upazila_ids')->nullable();
            // $table->json('target_user_ids')->nullable();

            $table->boolean('send_email_notification')->default(false);
            $table->boolean('show_on_dashboard')->default(true);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Pivot table for Announcement -> Roles
        Schema::create('announcement_role', function (Blueprint $table) {
            $table->id();
            $table->foreignId('announcement_id')->constrained('announcements')->cascadeOnDelete();
            $table->foreignId('role_id')->constrained('roles')->cascadeOnDelete(); // Assumes Spatie's roles table
            $table->timestamps();
            $table->unique(['announcement_id', 'role_id']);
        });

        // Pivot table for Announcement -> Divisions
        Schema::create('announcement_division', function (Blueprint $table) {
            $table->id();
            $table->foreignId('announcement_id')->constrained('announcements')->cascadeOnDelete();
            $table->foreignId('division_id')->constrained('divisions')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['announcement_id', 'division_id']);
        });

        // Pivot table for Announcement -> Districts
        Schema::create('announcement_district', function (Blueprint $table) {
            $table->id();
            $table->foreignId('announcement_id')->constrained('announcements')->cascadeOnDelete();
            $table->foreignId('district_id')->constrained('districts')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['announcement_id', 'district_id']);
        });

        // Pivot table for Announcement -> Upazilas
        Schema::create('announcement_upazila', function (Blueprint $table) {
            $table->id();
            $table->foreignId('announcement_id')->constrained('announcements')->cascadeOnDelete();
            $table->foreignId('upazila_id')->constrained('upazilas')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['announcement_id', 'upazila_id']);
        });

        // Pivot table for Announcement -> Users
        Schema::create('announcement_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('announcement_id')->constrained('announcements')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['announcement_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('announcement_user');
        Schema::dropIfExists('announcement_upazila');
        Schema::dropIfExists('announcement_district');
        Schema::dropIfExists('announcement_division');
        Schema::dropIfExists('announcement_role');
        Schema::dropIfExists('announcements');
    }
};
