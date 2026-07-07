<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('district_id')->nullable()->after('city_id')
                ->constrained('districts')->nullOnDelete();
            $table->timestamp('phone_verified_at')->nullable()->after('phone');
        });

        // Все существующие пользователи считаются подтверждёнными:
        // состояние «не активирован» вводится только для новых, созданных
        // из админки с опцией «Подтверждение по SMS».
        DB::table('users')->update(['phone_verified_at' => DB::raw('created_at')]);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('district_id');
            $table->dropColumn('phone_verified_at');
        });
    }
};
