<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->dropUnique('patients_identification_unique');
            $table->foreignId('user_id')->nullable()->after('id')->constrained('users')->cascadeOnDelete();
        });

        // Backfill user_id for existing patients
        // 1. Assign based on existing prescriptions
        DB::statement('
            UPDATE patients
            SET user_id = (
                SELECT user_id FROM prescriptions WHERE prescriptions.patient_id = patients.id LIMIT 1
            )
            WHERE user_id IS NULL AND EXISTS (
                SELECT 1 FROM prescriptions WHERE prescriptions.patient_id = patients.id
            )
        ');

        // 2. Assign based on existing appointments
        DB::statement('
            UPDATE patients
            SET user_id = (
                SELECT user_id FROM appointments WHERE appointments.patient_id = patients.id LIMIT 1
            )
            WHERE user_id IS NULL AND EXISTS (
                SELECT 1 FROM appointments WHERE appointments.patient_id = patients.id
            )
        ');

        // 3. Any remaining unassigned patients to first available user
        $defaultUserId = DB::table('users')->value('id') ?? 1;
        DB::statement("UPDATE patients SET user_id = {$defaultUserId} WHERE user_id IS NULL");

        // Add composite unique constraint so each doctor has unique patient identifications without cross-doctor conflicts
        Schema::table('patients', function (Blueprint $table) {
            $table->unique(['user_id', 'identification']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->dropUnique(['user_id', 'identification']);
            $table->dropConstrainedForeignId('user_id');
            $table->unique('identification');
        });
    }
};
