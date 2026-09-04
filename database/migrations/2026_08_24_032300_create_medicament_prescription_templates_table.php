<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('medicament_prescription_templates', function (Blueprint $table) {
            $table->id();
            $table->string('dosage');
            $table->string('frequency');
            $table->string('duration');
            $table->string('medicament_quantity');
            $table->string('medicament_quantity_letters');
            $table->string('recommended_brand')->nullable();

            $table->foreignId('medicament_id')->constrained()->cascadeOnDelete();
            $table->foreignId('prescription_template_id');
            $table->foreign('prescription_template_id', 'med_prescription_templates_template_fk')
                ->references('id')
                ->on('prescription_templates')
                ->cascadeOnDelete();

            $table->softDeletes();

            $table->unique(
                ['medicament_id', 'prescription_template_id'],
                'med_prescription_templates_med_template_unique',
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medicament_prescription_templates');
    }
};
