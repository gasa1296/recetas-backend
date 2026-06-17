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
        Schema::create('medicament_prescriptions', function (Blueprint $table) {
            $table->id();
            $table->string('dosage');
            $table->string('frequency');
            $table->string('duration');
            $table->string('medicament_quantity');
            $table->string('medicament_quantity_letters');

            $table->foreignId('medicament_id')->constrained()->cascadeOnDelete();
            $table->foreignId('prescription_id')->constrained()->cascadeOnDelete();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medicament_prescriptions');
    }
};
