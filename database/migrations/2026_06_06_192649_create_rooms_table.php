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
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('zip');
            $table->string('street');
            $table->string('colony');
            $table->string('state');
            $table->string('delegation');
            $table->string('n_exterior');
            $table->string('n_interior')->nullable();
            $table->string('address')->nullable();
            $table->json('phone');
            $table->boolean('fav')->default(false);
            $table->boolean('auto_email')->default(false);
            $table->boolean('auto_whatsapp')->default(false);
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
