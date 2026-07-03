<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('news', function (Blueprint $table) {
            $table->renameColumn('body_ru', 'content_ru');
            $table->renameColumn('body_tk', 'content_tk');
            $table->renameColumn('image_path', 'image');
        });

        Schema::table('news', function (Blueprint $table) {
            $table->foreignId('author_id')->nullable()->after('id')->constrained('users')->nullOnDelete();
        });

        DB::table('news')->where('type', 'regular')->update(['type' => 'news']);
        DB::table('news')->where('type', 'advertising')->update(['type' => 'advertisement']);

        // enum() в PostgreSQL создаёт check-constraint, который change() не снимает
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE news DROP CONSTRAINT IF EXISTS news_type_check');
        }

        Schema::table('news', function (Blueprint $table) {
            $table->string('title_tk')->nullable()->change();
            $table->longText('content_ru')->nullable()->change();
            $table->longText('content_tk')->nullable()->change();
            $table->string('type')->default('news')->change();
        });
    }

    public function down(): void
    {
        DB::table('news')->where('type', 'news')->update(['type' => 'regular']);
        DB::table('news')->where('type', 'advertisement')->update(['type' => 'advertising']);
        DB::table('news')->where('type', 'update')->update(['type' => 'regular']);

        Schema::table('news', function (Blueprint $table) {
            $table->string('title_tk')->nullable(false)->change();
            $table->longText('content_ru')->nullable(false)->change();
            $table->longText('content_tk')->nullable(false)->change();
            $table->string('type')->default('regular')->change();
        });

        Schema::table('news', function (Blueprint $table) {
            $table->dropConstrainedForeignId('author_id');
        });

        Schema::table('news', function (Blueprint $table) {
            $table->renameColumn('content_ru', 'body_ru');
            $table->renameColumn('content_tk', 'body_tk');
            $table->renameColumn('image', 'image_path');
        });
    }
};
