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
            $table->boolean('fav')->default(false);
            $table->boolean('auto_email')->default(false);
            $table->boolean('auto_whatsapp')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->dropColumn('fav');
            $table->dropColumn('auto_email');
            $table->dropColumn('auto_whatsapp');
        });
    }
};
