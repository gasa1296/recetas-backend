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
            $table->text('diagnostic');
            $table->text('diet')->nullable();
            $table->text('add')->nullable();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('room_id')->constrained('consulting_rooms');
            $table->foreignId('patient_id')->constrained('patients');
            $table->string('file')->nullable();
            $table->integer('status')->default(0);
            
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
