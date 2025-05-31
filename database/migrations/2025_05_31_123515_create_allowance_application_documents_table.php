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
Schema::create('allowance_application_documents', function (Blueprint $table) {
    $table->id();
    $table->foreignId('allowance_application_id')->constrained('allowance_applications')->onDelete('cascade');
    $table->string('document_name');
    $table->string('file_path');
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('allowance_application_documents');
    }
};
