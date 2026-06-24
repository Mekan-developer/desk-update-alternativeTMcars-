<?php

namespace Database\Seeders;

use App\Models\Tariff;
use Illuminate\Database\Seeder;

class TariffSeeder extends Seeder
{
    public function run(): void
    {
        Tariff::create(['name' => 'Бесплатный', 'listings_limit' => 5,   'videos_limit' => 2,  'boosts_limit' => 3,  'duration_days' => 30, 'is_free' => true,  'is_active' => true]);
        Tariff::create(['name' => 'Стандарт',   'listings_limit' => 20,  'videos_limit' => 10, 'boosts_limit' => 10, 'duration_days' => 30, 'is_free' => false, 'is_active' => true]);
        Tariff::create(['name' => 'Премиум',    'listings_limit' => 100, 'videos_limit' => 50, 'boosts_limit' => 50, 'duration_days' => 30, 'is_free' => false, 'is_active' => true]);
    }
}
