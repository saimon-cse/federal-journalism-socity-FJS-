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
        // database/migrations/xxxx_xx_xx_xxxxxx_create_training_schedules_table.php
Schema::create('training_schedules', function (Blueprint $table) {
    $table->id();
    $table->foreignId('training_id')->constrained()->onDelete('cascade');
    $table->foreignId('resource_person_id')->nullable()->constrained('resource_persons')->onDelete('set null'); // Instructor for this slot
    $table->string('topic_or_module_name');
    $table->date('schedule_date');
    $table->time('start_time');
    $table->time('end_time');
    $table->text('details')->nullable();
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('training_schedules');
    }
};
