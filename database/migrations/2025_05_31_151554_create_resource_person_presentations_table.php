<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('resource_person_presentations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('resource_person_id')->constrained('resource_persons')->onDelete('cascade');
            $table->string('title');
            $table->string('file_path');
            $table->text('description')->nullable();
            $table->boolean('is_approved_by_admin')->default(false);
            $table->foreignId('approved_by_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('resource_person_presentations');
    }
};
