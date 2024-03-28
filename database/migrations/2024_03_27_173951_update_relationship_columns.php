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
        Schema::table('consulting_rooms', function (Blueprint $table) {
            $table->foreignId('user_id')->change()->constrained(table:'users', indexName: 'room_user')->onDelete('cascade');
        });
        Schema::table('patients', function (Blueprint $table) {
            $table->foreignId('user_id')->change()->constrained(table:'users', indexName: 'patients_user')->onDelete('cascade');
        });
        Schema::table('prescriptions', function (Blueprint $table) {
            $table->foreignId('user_id')->change()->constrained(table:'users', indexName: 'prescriptions_user')->onDelete('cascade');
            $table->foreignId('room_id')->change()->constrained(table: 'consulting_rooms', indexName: 'prescriptions_room')->onDelete('cascade');
            $table->foreignId('patient_id')->change()->constrained(table: 'users', indexName: 'prescriptions_patient')->onDelete('cascade');
        });
        Schema::table('prescription_medicaments', function (Blueprint $table) {
            $table->foreignId('prescription_id')->change()->constrained(table: 'prescriptions', indexName: 'prescription_medicaments_prescription')->onDelete('cascade');
        });
        Schema::table('prescription_equipment', function (Blueprint $table) {
            $table->foreignId('equipment_id')->change()->constrained(table: 'equipment', indexName: 'prescription_equipment_equipment')->onDelete('cascade');
            $table->foreignId('prescription_id')->change()->constrained(table: 'prescriptions', indexName: 'prescription_equipment_prescription')->onDelete('cascade');
        });
        Schema::table('specializations', function (Blueprint $table) {
            $table->foreignId('user_id')->change()->constrained(table:'users', indexName: 'specializations_user')->onDelete('cascade');
        });
        Schema::table('documents', function (Blueprint $table) {
            $table->foreignId('prescription_id')->change()->constrained(table: 'prescriptions', indexName: 'documents_prescription')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    }
};
