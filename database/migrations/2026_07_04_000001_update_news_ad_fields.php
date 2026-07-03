<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Конвертируем старые значения типов: news/advertisement/update → regular/ad
        DB::table('news')->where('type', '!=', 'advertisement')->update(['type' => 'regular']);
        DB::table('news')->where('type', 'advertisement')->update(['type' => 'ad']);

        Schema::table('news', function (Blueprint $table) {
            // Колонка type уже существует, но значения изменились
            $table->string('ad_link_type')->nullable()->after('type');  // profile|listing|product
            $table->unsignedBigInteger('ad_link_id')->nullable()->after('ad_link_type');
        });
    }

    public function down(): void
    {
        // Конвертируем обратно: regular/ad → news/advertisement
        DB::table('news')->where('type', 'regular')->update(['type' => 'news']);
        DB::table('news')->where('type', 'ad')->update(['type' => 'advertisement']);

        Schema::table('news', function (Blueprint $table) {
            $table->dropColumn('ad_link_type', 'ad_link_id');
        });
    }
};
