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
        Schema::create('laboratories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('code')->nullable();
            $table->string('country')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('brands', function (Blueprint $table) {
            $table->id();
            $table->foreignId('laboratory_id')->constrained('laboratories')->cascadeOnDelete();
            $table->string('name');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['laboratory_id', 'name']);
        });

        Schema::table('medicament_prescriptions', function (Blueprint $table) {
            $table->foreignId('brand_id')->nullable()->after('recommended_brand')->constrained('brands')->nullOnDelete();
            $table->foreignId('laboratory_id')->nullable()->after('brand_id')->constrained('laboratories')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('medicament_prescriptions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('laboratory_id');
            $table->dropConstrainedForeignId('brand_id');
        });

        Schema::dropIfExists('brands');
        Schema::dropIfExists('laboratories');
    }
};
