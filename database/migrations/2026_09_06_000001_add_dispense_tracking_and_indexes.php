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
        Schema::table('prescriptions', function (Blueprint $table) {
            $table->foreignId('dispensed_by_id')->nullable()->after('status')->constrained('users')->nullOnDelete();
            $table->timestamp('dispensed_at')->nullable()->after('dispensed_by_id');

            $table->index(['user_id', 'created_at']);
            $table->index(['user_id', 'status']);
        });

        Schema::table('medicaments', function (Blueprint $table) {
            $table->index('active_ingredient');
            $table->index('type');
            $table->index('group');
        });

        Schema::table('appointments', function (Blueprint $table) {
            $table->index(['room_id', 'starts_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropIndex(['room_id', 'starts_at']);
        });

        Schema::table('medicaments', function (Blueprint $table) {
            $table->dropIndex(['active_ingredient']);
            $table->dropIndex(['type']);
            $table->dropIndex(['group']);
        });

        Schema::table('prescriptions', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'created_at']);
            $table->dropIndex(['user_id', 'status']);
            $table->dropForeign(['dispensed_by_id']);
            $table->dropColumn(['dispensed_by_id', 'dispensed_at']);
        });
    }
};
