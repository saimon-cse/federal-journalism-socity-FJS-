<?php
// create_divisions_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('divisions', function (Blueprint $table) {
            $table->id();
            $table->string('name_en')->unique();
            $table->string('name_bn')->unique();
            $table->string('slug')->unique(); // Unique slug for URL usage

            $table->timestamps();
        });
    }
    public function down() { Schema::dropIfExists('divisions'); }
};
