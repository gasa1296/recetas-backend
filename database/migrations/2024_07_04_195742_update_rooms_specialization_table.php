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
            $table->string('id_ext')->default(false);
        });
        Schema::table('specializations', function (Blueprint $table) {
            $table->string('id_ext')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('consulting_rooms', function (Blueprint $table) {
            $table->dropColumn('id_ext');
        });
        Schema::table('specializations', function (Blueprint $table) {
            $table->string('id_ext')->default(false);
        });
    }
};
