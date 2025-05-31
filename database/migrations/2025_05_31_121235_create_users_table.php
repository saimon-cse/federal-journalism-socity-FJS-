<?php

// database/migrations/xxxx_xx_xx_xxxxxx_create_users_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id(); // user_id in other tables will refer to this
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('profile_picture')->nullable();
            // Personal Info
            $table->string('father_name')->nullable();
            $table->string('mother_name')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('blood_group', 5)->nullable(); // A+, O- etc.
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->string('religion')->nullable();
            // Contact Info
            $table->string('phone')->unique()->nullable(); // Make unique if it's a primary contact
            $table->string('whatsapp_number')->nullable();
            // Identification
            $table->string('nid_number')->unique()->nullable();
            $table->string('passport_number')->unique()->nullable();
            // Professional Info
            $table->string('designation')->nullable();
            $table->string('organization_name')->nullable(); // Renamed from organization to avoid conflict
            // Location - For primary working area or current address
            $table->unsignedBigInteger('division_id')->nullable();
            $table->unsignedBigInteger('district_id')->nullable();
            $table->unsignedBigInteger('upazila_id')->nullable();
            // Address details (can be moved to a separate address table if multiple addresses needed per user)
            $table->text('present_address')->nullable(); // Or specific fields: house, road, area etc.
            $table->text('permanent_address')->nullable();
            // Other settings
            $table->boolean('newsletter_subscribed')->default(false);
            $table->boolean('is_public_profile')->default(false); // Renamed from is_public
            $table->boolean('is_active')->default(true); // For activating/deactivating users
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();

            // Foreign keys for location (assuming you have location tables)
            $table->foreign('division_id')->references('id')->on('divisions')->onDelete('set null');
            $table->foreign('district_id')->references('id')->on('districts')->onDelete('set null');
            $table->foreign('upazila_id')->references('id')->on('upazilas')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
};
