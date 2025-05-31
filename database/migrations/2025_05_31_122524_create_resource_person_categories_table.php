<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // database/migrations/xxxx_xx_xx_xxxxxx_create_resource_person_categories_table.php
Schema::create('resource_person_categories', function (Blueprint $table) {
    $table->id();
    $table->string('name')->unique(); // সাংবাদিকতা বিষয়ক, স্কিল ডেভেলপমেন্ট, আইটি
    $table->boolean('show_on_web')->default(false);
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resource_person_categories');
    }
};
