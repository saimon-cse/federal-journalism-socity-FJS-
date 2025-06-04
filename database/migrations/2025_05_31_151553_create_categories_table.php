<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type');//resorse persoon, membership, training, event, etc.
            // e.g., 'resource_person', 'membership', 'training', 'event'
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['name', 'type']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('categories');
    }
};
