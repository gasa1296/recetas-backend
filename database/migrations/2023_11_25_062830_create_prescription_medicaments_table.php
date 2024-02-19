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
            $table->string('add')->nullable();
            $table->string('dose');
            $table->string('way');
            $table->string('frequency');
            $table->string('duration');
            $table->unsignedInteger('quantity');
            $table->unsignedInteger('quantity_exp')->default(0);
            $table->string('medicament_id');
            $table->string('name');
            $table->string('type');
            $table->string('family');
            $table->string('group');
            $table->string('salt');
            $table->foreignId('prescription_id')->constrained('prescriptions');
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
