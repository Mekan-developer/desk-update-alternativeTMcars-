<?php

namespace App\Jobs;

use App\Models\ListingMedia;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Encoders\WebpEncoder;
use Intervention\Image\ImageManager;

class ProcessListingImagesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 30;

    public function __construct(
        private readonly int $listingMediaId,
        private readonly string $originalPath,
        private readonly int $listingId,
    ) {
        $this->onQueue('media');
    }

    public function handle(): void
    {
        $media   = ListingMedia::findOrFail($this->listingMediaId);
        $manager = new ImageManager(new Driver());

        $basePath = "listings/{$this->listingId}/photos";
        $fileName = pathinfo($this->originalPath, PATHINFO_FILENAME);

        $original = Storage::disk('public')->get($this->originalPath);

        // Приложение только для телефонов (не планшетов) — размеры рассчитаны
        // под этот экран, не под большие дисплеи:

        // Original: max 1080px (полноэкранный просмотр карточки, ~3x DPR на 360dp)
        $img = $manager->decodeBinary($original);
        $img->scaleDown(width: 1080, height: 1080);
        $originalWebp = "{$basePath}/original/{$fileName}.webp";
        Storage::disk('public')->put($originalWebp, $img->encode(new WebpEncoder(quality: 82))->toString());

        // Medium: 480×480 fit (карточки в списке/сетке)
        $img = $manager->decodeBinary($original);
        $img->cover(480, 480);
        $mediumWebp = "{$basePath}/medium/{$fileName}.webp";
        Storage::disk('public')->put($mediumWebp, $img->encode(new WebpEncoder(quality: 78))->toString());

        // Thumb: 120×120 crop (миниатюры, превью в чате)
        $img = $manager->decodeBinary($original);
        $img->cover(120, 120);
        $thumbWebp = "{$basePath}/thumb/{$fileName}.webp";
        Storage::disk('public')->put($thumbWebp, $img->encode(new WebpEncoder(quality: 72))->toString());

        // `path` остаётся рабочей ссылкой в любой момент: до обработки — загруженный
        // оригинал, после — WebP-оригинал (админка рендерит именно `path`)
        $media->update([
            'path'        => $originalWebp,
            'medium_path' => $mediumWebp,
            'thumb_path'  => $thumbWebp,
        ]);

        Storage::disk('public')->delete($this->originalPath);
    }
}
