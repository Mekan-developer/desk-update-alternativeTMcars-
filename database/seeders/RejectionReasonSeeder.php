<?php

namespace Database\Seeders;

use App\Models\RejectionReason;
use Illuminate\Database\Seeder;

class RejectionReasonSeeder extends Seeder
{
    public function run(): void
    {
        $rejections = [
            ['name_ru' => 'Неприемлемый контент',     'name_tk' => 'Kabul edilmeýän mazmun',  'type' => 'listing'],
            ['name_ru' => 'Неверная категория',        'name_tk' => 'Nädogry kategoriýa',       'type' => 'listing'],
            ['name_ru' => 'Дублирующее объявление',    'name_tk' => 'Gaýtalanýan bildiriş',     'type' => 'listing'],
            ['name_ru' => 'Недостаточно фото',         'name_tk' => 'Surat ýeterlik däl',       'type' => 'listing'],
            ['name_ru' => 'Запрещённый товар',         'name_tk' => 'Gadagan edilen haryt',     'type' => 'listing'],
            ['name_ru' => 'Неприемлемое видео',        'name_tk' => 'Kabul edilmeýän wideo',    'type' => 'video'],
            ['name_ru' => 'Оскорбительный отзыв',      'name_tk' => 'Kemsidiji syn',            'type' => 'review'],
        ];

        foreach ($rejections as $r) {
            RejectionReason::create($r);
        }
    }
}
