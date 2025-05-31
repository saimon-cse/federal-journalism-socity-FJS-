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
       // database/migrations/xxxx_xx_xx_xxxxxx_create_resource_person_presentations_table.php
Schema::create('resource_person_presentations', function (Blueprint $table) {
    $table->id();
    $table->foreignId('resource_person_id')->constrained('resource_persons')->onDelete('cascade');
    // $table->foreignId('training_id')->nullable()->constrained()->onDelete('set null'); // Optional: link to a specific training
    $table->string('subject');
    $table->string('file_path'); // Path to the uploaded presentation
    $table->string('original_file_name')->nullable();
    $table->boolean('is_admin_approved')->default(false);
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resource_person_presentations');
    }
};
