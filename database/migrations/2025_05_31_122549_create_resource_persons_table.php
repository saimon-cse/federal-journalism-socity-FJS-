<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('resource_persons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->onDelete('cascade'); // Each user can be a resource person once
            $table->foreignId('category_id')->nullable()->constrained('resource_person_categories')->onDelete('set null');
            $table->text('bio')->nullable();
            $table->decimal('hourly_rate', 8, 2)->nullable(); // Example field
            // Other specific details for resource persons
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('resource_persons');
    }
};
