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
        Schema::create('prescriptions', function (Blueprint $table) {
            $table->id();
            $table->float('temp')->nullable();
            $table->float('weight')->nullable();
            $table->float('height')->nullable();
            $table->string('pressure')->nullable();
            $table->string('saturation')->nullable();
            $table->string('ppm')->nullable();
            $table->text('allergy')->nullable();
            $table->text('diagnostic')->nullable();
            $table->text('diet')->nullable();
            $table->text('comments')->nullable();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('room_id')->constrained('rooms')->cascadeOnDelete();
            $table->foreignId('patient_id')->constrained('patients')->cascadeOnDelete();
            $table->integer('status')->default(0);
            $table->timestamps(6);
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prescriptions');
    }
};
