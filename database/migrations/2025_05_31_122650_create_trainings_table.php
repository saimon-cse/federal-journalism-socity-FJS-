<?php

// database/migrations/xxxx_xx_xx_xxxxxx_create_trainings_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('trainings', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->foreignId('training_category_id')->nullable()->constrained('training_categories')->onDelete('set null');
            $table->text('description')->nullable();
            $table->string('batch_number')->nullable();
            $table->enum('type', ['online', 'offline']);
            $table->string('venue')->nullable(); // For offline
            $table->enum('payment_type', ['paid', 'free'])->default('free');
            $table->decimal('fee', 10, 2)->nullable(); // If paid
            $table->enum('participant_type', ['member', 'non_member', 'both'])->default('both');
            $table->integer('max_participants')->nullable();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->string('duration_description')->nullable(); // e.g., "3 days", "5 weeks"
            $table->integer('total_hours')->nullable();
            $table->string('banner_image')->nullable(); // Image for the training
            $table->string('youtube_link')->nullable(); // Video link
            $table->enum('status', ['upcoming', 'ongoing', 'completed', 'cancelled'])->default('upcoming');
            $table->boolean('is_published')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('trainings');
    }
};
