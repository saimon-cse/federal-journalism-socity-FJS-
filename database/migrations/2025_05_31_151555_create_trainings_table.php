<?php

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
            $table->string('batch_number')->nullable();
            $table->foreignId('category_id')->nullable()->constrained('categories')->onDelete('set null');
            $table->string('payment_status_option');
            $table->string('participant_scope');
            $table->decimal('fee_amount', 10, 2)->nullable();
            $table->string('mode');
            $table->date('start_date');
            $table->date('end_date');
            $table->unsignedInteger('total_duration_hours')->nullable();
            $table->string('venue_details')->nullable();
            $table->unsignedInteger('max_participants')->nullable();
            $table->string('cover_image_path')->nullable();
            $table->string('youtube_link')->nullable();
            $table->foreignId('created_by_user_id')->constrained('users')->onDelete('cascade');
            $table->string('status')->default('pending_approval');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('trainings');
    }
};
