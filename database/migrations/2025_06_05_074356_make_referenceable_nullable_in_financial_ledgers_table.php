<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
  public function up()
{
    Schema::table('financial_ledgers', function (Blueprint $table) {
        $table->unsignedBigInteger('referenceable_id')->nullable()->change();
        $table->string('referenceable_type')->nullable()->change();
    });
}
public function down()
{
    Schema::table('financial_ledgers', function (Blueprint $table) {
        // Revert if possible, though making non-nullable can fail if nulls exist
        $table->unsignedBigInteger('referenceable_id')->nullable(false)->change();
        $table->string('referenceable_type')->nullable(false)->change();
    });
}
};
