<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Encoders\WebpEncoder;
use Intervention\Image\ImageManager;
use Intervention\Image\Interfaces\ImageInterface;

/**
 * Глобальная конвертация загружаемых изображений в WebP.
 *
 * Используется для любых медиа-загрузок (обложки новостей, фото объявлений и т.д.):
 * опциональный кроп под заданное соотношение сторон по координатам в процентах
 * (семантика CSS background-position — как в превью на фронтенде), ресайз под
 * мобильные экраны и подбор качества, чтобы файл уложился в заданный вес.
 */
class ImageConversionService
{
    private const QUALITY_START = 80;
    private const QUALITY_MIN   = 40;
    private const QUALITY_STEP  = 10;

    /**
     * Конвертирует файл в WebP и кладёт на диск.
     *
     * @param UploadedFile $file     исходное изображение (jpg/png/webp)
     * @param string       $dir      директория на диске (например 'news')
     * @param float|null   $aspect   целевое соотношение сторон (16/9); null — без кропа
     * @param float        $cropX    позиция кропа по X, 0–100 (background-position)
     * @param float        $cropY    позиция кропа по Y, 0–100
     * @param int          $maxWidth максимальная ширина результата, px
     * @param int          $maxBytes целевой вес файла (качество подбирается под него)
     * @param string       $disk
     *
     * @return string путь сохранённого файла на диске
     */
    public function toWebp(
        UploadedFile $file,
        string $dir,
        ?float $aspect = null,
        float $cropX = 50,
        float $cropY = 50,
        int $maxWidth = 1200,
        int $maxBytes = 102400,
        string $disk = 'public',
    ): string {
        $manager = new ImageManager(new Driver());
        $img     = $manager->decodePath($file->getRealPath());

        if ($aspect !== null) {
            $this->cropToAspect($img, $aspect, $cropX, $cropY);
        }

        $img->scaleDown(width: $maxWidth);

        $encoded = $this->encodeUnderLimit($img, $maxBytes);

        $path = trim($dir, '/').'/'.Str::uuid().'.webp';
        Storage::disk($disk)->put($path, $encoded);

        return $path;
    }

    /**
     * Вырезает максимальный прямоугольник заданного соотношения.
     * Смещение — как у background-position: X% исходника совмещается с X% кадра.
     */
    private function cropToAspect(ImageInterface $img, float $aspect, float $cropX, float $cropY): void
    {
        $w = $img->width();
        $h = $img->height();

        if ($w / $h > $aspect) {
            $cropH = $h;
            $cropW = (int) round($h * $aspect);
        } else {
            $cropW = $w;
            $cropH = (int) round($w / $aspect);
        }

        $x = (int) round(($w - $cropW) * $this->clampPercent($cropX) / 100);
        $y = (int) round(($h - $cropH) * $this->clampPercent($cropY) / 100);

        $img->crop($cropW, $cropH, $x, $y);
    }

    /**
     * Понижает качество (и в крайнем случае размер), пока файл не влезет в лимит.
     */
    private function encodeUnderLimit(ImageInterface $img, int $maxBytes): string
    {
        $encoded = '';

        for ($quality = self::QUALITY_START; $quality >= self::QUALITY_MIN; $quality -= self::QUALITY_STEP) {
            $encoded = $img->encode(new WebpEncoder(quality: $quality))->toString();
            if (strlen($encoded) <= $maxBytes) {
                return $encoded;
            }
        }

        // Минимальное качество не помогло — ужимаем размер (до 2 попыток по -25%)
        for ($i = 0; $i < 2 && strlen($encoded) > $maxBytes; $i++) {
            $img->scaleDown(width: (int) round($img->width() * 0.75));
            $encoded = $img->encode(new WebpEncoder(quality: self::QUALITY_MIN))->toString();
        }

        return $encoded;
    }

    private function clampPercent(float $value): float
    {
        return max(0, min(100, $value));
    }
}
