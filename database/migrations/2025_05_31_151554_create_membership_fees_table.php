<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('membership_fees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('fee_type');
            $table->unsignedInteger('year');
            $table->unsignedTinyInteger('month')->nullable();
            $table->decimal('amount_due', 10, 2);
            $table->date('due_date');
            $table->string('status')->default('pending'); // pending, paid, overdue, waived
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('membership_fees');
    }
};
