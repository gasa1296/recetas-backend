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
            $table->float('temp')->default(0);
            $table->float('weight')->default(0);
            $table->float('height')->default(0);
            $table->string('pressure');
            $table->string('saturation');
            $table->string('ppm');
            $table->text('allergy');
            $table->text('diagnostic');
            $table->text('diet');
            $table->text('aditional');
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('room_id')->constrained('consulting_rooms');
            $table->foreignId('patient_id')->constrained('patients');
            $table->string('file')->nullable();
            
            $table->timestamps();
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
