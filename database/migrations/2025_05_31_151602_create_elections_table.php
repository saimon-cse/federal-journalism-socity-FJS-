<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('elections', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->foreignId('target_committee_id')->nullable()->constrained('committees')->onDelete('set null');
            $table->timestamp('nomination_start_datetime');
            $table->timestamp('nomination_end_datetime')->nullable();
            $table->timestamp('withdrawal_end_datetime')->nullable();
            $table->timestamp('voting_start_datetime')->nullable();
            $table->timestamp('voting_end_datetime')->nullable();
            $table->timestamp('results_announced_datetime')->nullable();
            $table->decimal('nomination_fee', 10, 2)->nullable();
            $table->string('status')->default('upcoming');
            $table->foreignId('created_by_user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('elections');
    }
};
