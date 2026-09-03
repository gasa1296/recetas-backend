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
        Schema::create('examinations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patients')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('prescription_id')->nullable()->constrained('prescriptions')->nullOnDelete();
            $table->string('name');
            $table->string('type')->default('laboratory');
            $table->date('examined_at')->nullable();
            $table->string('laboratory_name')->nullable();
            $table->text('findings')->nullable();
            $table->string('status')->default('completed');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['patient_id', 'examined_at']);
            $table->index(['patient_id', 'type']);
            $table->index(['status', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('examinations');
    }
};
