<?php

namespace Database\Seeders;

use App\Models\CategoryIcon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

/**
 * Библиотека из 10 стандартных SVG-значков категорий (недвижимость, авто,
 * электроника и т.д.). Пользовательские значки, загруженные админом при
 * создании/редактировании категории, добавляются в эту же таблицу
 * (см. App\Actions\UploadCategoryIconAction) и попадают в общую библиотеку выбора.
 */
class CategoryIconSeeder extends Seeder
{
    private const ICONS = [
        'home' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#475569" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M4 11.5 12 4l8 7.5"/><path d="M6 10v9a1 1 0 0 0 1 1h10a1 1 0 0 0 1-1v-9"/><path d="M10 20v-5h4v5"/></svg>',
        'car' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#475569" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M4 16V11.5a1 1 0 0 1 .3-.7l2-2A2 2 0 0 1 7.7 8h8.6a2 2 0 0 1 1.4.6l2 2a1 1 0 0 1 .3.7V16"/><path d="M3.5 16h17v2.5a1 1 0 0 1-1 1h-1.5a1 1 0 0 1-1-1V17.5h-10V18.5a1 1 0 0 1-1 1H4.5a1 1 0 0 1-1-1z"/><circle cx="7.5" cy="16" r="1.4"/><circle cx="16.5" cy="16" r="1.4"/></svg>',
        'smartphone' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#475569" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><rect x="7" y="3" width="10" height="18" rx="2"/><path d="M11 18h2"/></svg>',
        'shirt' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#475569" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M8 4 4 7.5l2.5 3L8 9v11h8V9l1.5 1.5 2.5-3L16 4l-2 1.5h-4z"/></svg>',
        'briefcase' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#475569" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><rect x="3.5" y="7.5" width="17" height="11" rx="2"/><path d="M8.5 7.5V6a2 2 0 0 1 2-2h3a2 2 0 0 1 2 2v1.5"/><path d="M3.5 12.5h17"/></svg>',
        'shopping-bag' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#475569" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M6 8h12l-1 12a1.5 1.5 0 0 1-1.5 1.3h-7A1.5 1.5 0 0 1 7 20z"/><path d="M9 8V6a3 3 0 0 1 6 0v2"/></svg>',
        'wrench' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#475569" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a3.5 3.5 0 0 0-4.6 4.2L4 16.6V20h3.4l6.1-6.1a3.5 3.5 0 0 0 4.2-4.6l-2.6 2.6-2-2z"/></svg>',
        'gift' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#475569" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><rect x="3.5" y="9.5" width="17" height="4" rx="1"/><rect x="4.5" y="13.5" width="15" height="7" rx="1"/><path d="M12 9.5V20.5"/><path d="M12 9.5c-1-2.5-3-3.5-4.3-2.7C6.4 7.6 6.6 9.5 9 9.5z"/><path d="M12 9.5c1-2.5 3-3.5 4.3-2.7 1.3.8 1.1 2.7-1.3 2.7z"/></svg>',
        'dumbbell' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#475569" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M6.5 9v6"/><path d="M17.5 9v6"/><path d="M4 10.5v3"/><path d="M20 10.5v3"/><path d="M8.5 12h7"/></svg>',
        'paw' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#475569" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><circle cx="7" cy="8.5" r="1.6"/><circle cx="12" cy="6.5" r="1.6"/><circle cx="17" cy="8.5" r="1.6"/><path d="M9 15c0-2.3 1.3-4 3-4s3 1.7 3 4c0 1.8-1.4 3-3 3s-3-1.2-3-3z"/><circle cx="4.7" cy="12.5" r="1.4"/><circle cx="19.3" cy="12.5" r="1.4"/></svg>',
    ];

    public function run(): void
    {
        foreach (self::ICONS as $slug => $svg) {
            if (CategoryIcon::where('slug', $slug)->exists()) {
                continue;
            }

            $path = "categories/icons/{$slug}.svg";
            Storage::disk('public')->put($path, $svg);

            CategoryIcon::create([
                'slug' => $slug,
                'path' => $path,
                'is_system' => true,
            ]);
        }
    }
}
