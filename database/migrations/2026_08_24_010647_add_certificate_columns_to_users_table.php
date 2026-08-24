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
            $table->string('certificate_path')->nullable()->after('signature_hash');
            $table->string('certificate_key_path')->nullable()->after('certificate_path');
            $table->timestamp('certificate_expires_at')->nullable()->after('certificate_key_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['certificate_path', 'certificate_key_path', 'certificate_expires_at']);
        });
    }
};
