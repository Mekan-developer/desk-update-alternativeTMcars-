<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Тарифы создавались с одноязычным `name` и `boosts_limit`, но весь остальной
 * код (StoreTariffRequest, Vue-форма, lang-файлы) уже написан под билингвальные
 * name_ru/name_tk и единственное число boost_limit — из-за рассинхрона создание
 * тарифа из админки падало на NOT NULL `name`, а сохранение лимита поднятий
 * тихо терялось (mass-assignment отбрасывал несуществующий атрибут).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tariffs', function (Blueprint $table) {
            $table->string('name_ru')->nullable()->after('name');
            $table->string('name_tk')->nullable()->after('name_ru');
        });

        DB::table('tariffs')->update([
            'name_ru' => DB::raw('name'),
            'name_tk' => DB::raw('name'),
        ]);

        Schema::table('tariffs', function (Blueprint $table) {
            $table->dropColumn('name');
        });

        Schema::table('tariffs', function (Blueprint $table) {
            $table->renameColumn('boosts_limit', 'boost_limit');
        });
    }

    public function down(): void
    {
        Schema::table('tariffs', function (Blueprint $table) {
            $table->string('name')->nullable();
        });

        DB::table('tariffs')->update(['name' => DB::raw('name_ru')]);

        Schema::table('tariffs', function (Blueprint $table) {
            $table->dropColumn(['name_ru', 'name_tk']);
        });

        Schema::table('tariffs', function (Blueprint $table) {
            $table->renameColumn('boost_limit', 'boosts_limit');
        });
    }
};
