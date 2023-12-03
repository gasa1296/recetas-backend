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
            $table->id();
            $table->string('dose')->nullable();
            $table->string('way');
            $table->string('frequency')->nullable();
            $table->string('duration');
            $table->foreignId('medicament_id')->constrained('medicaments');
            $table->foreignId('prescription_id')->constrained('prescriptions');
            $table->timestamps();

            $table->unique(['medicament_id','prescription_id']);
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
