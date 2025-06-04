<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Modify the trainings table to remove old text columns
        if (Schema::hasTable('trainings')) {
            Schema::table('trainings', function (Blueprint $table) {
                if (Schema::hasColumn('trainings', 'custom_id_card_settings')) {
                    $table->dropColumn('custom_id_card_settings');
                }
                if (Schema::hasColumn('trainings', 'custom_certificate_settings')) {
                    $table->dropColumn('custom_certificate_settings');
                }
            });
        }

        // New table for training-specific template settings (ID card, Certificate)
        Schema::create('training_template_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('training_id')->constrained('trainings')->cascadeOnDelete();
            $table->string('template_type')->comment('e.g., id_card, certificate');
            $table->string('setting_key');
            $table->text('setting_value')->nullable();
            $table->timestamps();

            $table->unique(['training_id', 'template_type', 'setting_key'], 'training_template_setting_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('training_template_settings');

        // Add back the columns if rolling back (optional, depends on your rollback strategy)
        if (Schema::hasTable('trainings')) {
            Schema::table('trainings', function (Blueprint $table) {
                if (!Schema::hasColumn('trainings', 'custom_id_card_settings')) {
                    $table->text('custom_id_card_settings')->nullable()->comment('JSON config for ID card template, if training-specific.');
                }
                if (!Schema::hasColumn('trainings', 'custom_certificate_settings')) {
                    $table->text('custom_certificate_settings')->nullable()->comment('JSON config for certificate template, if training-specific.');
                }
            });
        }
    }
};
