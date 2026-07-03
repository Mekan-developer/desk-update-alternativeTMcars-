<?php

namespace App\Actions;

use App\Repositories\Interfaces\CategoryIconRepositoryInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

/**
 * Сохраняет загруженный SVG-значок категории на диск и регистрирует его
 * в общей библиотеке иконок, чтобы он предлагался при выборе для других категорий.
 */
class UploadCategoryIconAction
{
    public function __construct(
        private readonly CategoryIconRepositoryInterface $icons,
    ) {}

    public function execute(UploadedFile $file): string
    {
        $path = $file->store('categories/icons', 'public');

        $this->icons->create([
            'slug' => Str::uuid()->toString(),
            'path' => $path,
            'is_system' => false,
        ]);

        return $path;
    }
}
