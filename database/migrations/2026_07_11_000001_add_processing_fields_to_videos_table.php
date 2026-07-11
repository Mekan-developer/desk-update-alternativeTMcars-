<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ролики (ТЗ §7): `path` остаётся оригиналом загрузки, сжатая версия и
     * превью-кадр появляются после фоновой обработки FFmpeg (ProcessVideoJob).
     */
    public function up(): void
    {
        Schema::table('videos', function (Blueprint $table) {
            $table->string('processed_path')->nullable()->after('path');
            $table->string('preview_path')->nullable()->after('processed_path');
            $table->unsignedSmallInteger('duration_seconds')->default(0)->after('preview_path');
            $table->boolean('is_processed')->default(false)->after('duration_seconds');
        });
    }

    public function down(): void
    {
        Schema::table('videos', function (Blueprint $table) {
            $table->dropColumn(['processed_path', 'preview_path', 'duration_seconds', 'is_processed']);
        });
    }
};
