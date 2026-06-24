<?php

namespace Database\Seeders;

use App\Models\ComplaintReason;
use Illuminate\Database\Seeder;

class ComplaintReasonSeeder extends Seeder
{
    public function run(): void
    {
        $complaints = [
            'Мошенничество'       => 'Galplyk',
            'Запрещённый контент' => 'Gadagan edilen mazmun',
            'Неверная информация' => 'Nädogry maglumat',
            'Спам'                => 'Spam',
            'Другое'              => 'Beýleki',
        ];

        foreach ($complaints as $ru => $tk) {
            ComplaintReason::create(['name_ru' => $ru, 'name_tk' => $tk]);
        }
    }
}
