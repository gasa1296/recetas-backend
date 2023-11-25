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
        Schema::create('prescription_medicaments', function (Blueprint $table) {
            $table->string('dose')->nullable();
            $table->string('way');
            $table->string('frequency')->nullable();
            $table->string('duration');
            $table->foreignId('medicament_id')->constrained();
            $table->foreignId('prescription_id')->constrained();
            $table->primary(['medicament_id', 'prescription_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prescription_medicaments');
    }
};
