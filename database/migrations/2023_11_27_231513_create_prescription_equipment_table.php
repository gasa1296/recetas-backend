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
        Schema::create('prescription_equipment', function (Blueprint $table) {
            $table->text('add')->nullable();
            $table->foreignId('equipment_id')->constrained('equipment');
            $table->foreignId('prescription_id')->constrained('prescriptions');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prescription_equipment');
    }
};
