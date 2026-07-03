<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RegionSeeder::class,
            TariffSeeder::class,
            CategorySeeder::class,
            CategoryIconSeeder::class,
            RejectionReasonSeeder::class,
            ComplaintReasonSeeder::class,
            UserSeeder::class,
        ]);
    }
}
