<?php
// database/migrations/xxxx_xx_xx_xxxxxx_create_committees_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('committees', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->enum('level', ['central', 'division', 'district', 'upazila', 'other'])->default('other');
            $table->text('description')->nullable();
            $table->foreignId('division_id')->nullable()->constrained('divisions')->nullOnDelete();
            $table->foreignId('district_id')->nullable()->constrained('districts')->nullOnDelete();
            $table->foreignId('upazila_id')->nullable()->constrained('upazilas')->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->date('formation_date')->nullable(); // Date when the committee was established
            $table->date('term_start_date')->nullable();
            $table->date('term_end_date')->nullable();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });


        Schema::create('committee_positions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // e.g., Chairperson, Secretary, Member
            $table->string('slug')->unique(); // Unique slug for URL usage
            $table->text('description')->nullable(); // Description of the position
            $table->boolean('is_active')->default(true); // If the position is currently active
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('committee_id')->constrained('committees')->cascadeOnDelete(); // Reference to the committee this position belongs to
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });


        Schema::create('committee_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('committee_id')->constrained('committees')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('position_id')->nullable()->constrained('committee_positions')->nullOnDelete()->comment('Reference to committee_positions table');
            $table->boolean('is_manager')->default(false)->comment('If this member is the designated Committee Manager for this committee');
            $table->date('appointed_on')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('status')->default('active'); // e.g., active, inactive, resigned, removed
            $table->timestamps();

            $table->unique(['committee_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('committee_members');
        Schema::dropIfExists('committee_positions');
        Schema::dropIfExists('committees');

    }
};
