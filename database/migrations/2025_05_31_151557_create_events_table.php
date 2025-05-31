<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('event_code')->unique()->nullable();
            $table->string('cover_image_path')->nullable();
            $table->string('youtube_link')->nullable();
            $table->string('location_details');
            $table->dateTime('event_datetime');
            $table->text('description')->nullable();
            $table->boolean('is_paid')->default(false);
            $table->decimal('fee_amount', 10, 2)->nullable();
            $table->string('participant_scope');
            $table->foreignId('created_by_user_id')->constrained('users')->onDelete('cascade');
            $table->string('status')->default('upcoming');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('events');
    }
};
