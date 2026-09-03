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
        Schema::table('files', function (Blueprint $table) {
            $table->string('category')->default('general')->after('type');
            $table->string('title')->nullable()->after('category');
            $table->text('description')->nullable()->after('title');
            $table->string('mime_type')->nullable()->after('description');
            $table->unsignedBigInteger('size')->nullable()->after('mime_type');
            $table->json('meta')->nullable()->after('size');
            $table->foreignId('user_id')->nullable()->after('meta')->constrained('users')->nullOnDelete();
            $table->softDeletes()->after('updated_at');

            $table->index(['model_type', 'model_id', 'category']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('files', function (Blueprint $table) {
            $table->dropIndex(['model_type', 'model_id', 'category']);
            $table->dropForeign(['user_id']);
            $table->dropColumn([
                'category',
                'title',
                'description',
                'mime_type',
                'size',
                'meta',
                'user_id',
                'deleted_at',
            ]);
        });
    }
};
