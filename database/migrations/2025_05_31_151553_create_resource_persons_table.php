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
            $table->foreignId('user_id')->unique()->constrained('users')->onDelete('cascade');
            $table->foreignId('category_id')->nullable()->constrained('categories')->onDelete('set null');
            $table->boolean('is_visible_on_website')->default(false);
            // Bio moved to user_profiles
            $table->string('expertise_areas')->nullable(); // Consider a pivot table resource_person_expertise for multiple
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('resource_persons');
    }
};
