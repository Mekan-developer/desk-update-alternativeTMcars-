<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $catData = [
            ['name_ru' => 'Оптом',   'name_tk' => 'Optom',    'children' => ['Продукты питания', 'Стройматериалы', 'Одежда']],
            ['name_ru' => 'Розница', 'name_tk' => 'Bölekleýin', 'children' => ['Электроника', 'Мебель', 'Одежда и обувь']],
            ['name_ru' => 'Услуги',  'name_tk' => 'Hyzmatlar', 'children' => ['Ремонт', 'Перевозки', 'Красота и здоровье']],
            ['name_ru' => 'Товары',  'name_tk' => 'Harytlar',  'children' => ['Авто', 'Недвижимость', 'Животные']],
        ];

        foreach ($catData as $i => $cd) {
            $parent = Category::create([
                'name_ru' => $cd['name_ru'], 'name_tk' => $cd['name_tk'],
                'slug' => Str::slug($cd['name_ru']), 'level' => 1, 'order' => $i + 1,
            ]);
            foreach ($cd['children'] as $j => $child) {
                Category::create([
                    'parent_id' => $parent->id, 'name_ru' => $child, 'name_tk' => $child,
                    'slug' => Str::slug($cd['name_ru'] . '-' . $child), 'level' => 2, 'order' => $j + 1,
                ]);
            }
        }
    }
}
