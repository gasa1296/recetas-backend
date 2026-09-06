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
        Schema::table('users', function (Blueprint $table) {
            $table->index('certificate_expires_at');
        });

        Schema::table('appointments', function (Blueprint $table) {
            $table->index(['reminder_enabled', 'reminder_sent_at', 'status', 'starts_at'], 'idx_appointments_reminders');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropIndex('idx_appointments_reminders');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['certificate_expires_at']);
        });
    }
};
