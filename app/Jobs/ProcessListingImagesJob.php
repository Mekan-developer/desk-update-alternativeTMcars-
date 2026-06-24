<?php

namespace App\Jobs;

use App\Models\ListingMedia;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

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

        // Original: max 1200px
        $img = $manager->read($original);
        $img->scaleDown(width: 1200, height: 1200);
        $originalWebp = "{$basePath}/original/{$fileName}.webp";
        Storage::disk('public')->put($originalWebp, $img->toWebp(85));

        // Medium: 600×600 fit
        $img = $manager->read($original);
        $img->cover(600, 600);
        $mediumWebp = "{$basePath}/medium/{$fileName}.webp";
        Storage::disk('public')->put($mediumWebp, $img->toWebp(80));

        // Thumb: 150×150 crop
        $img = $manager->read($original);
        $img->cover(150, 150);
        $thumbWebp = "{$basePath}/thumb/{$fileName}.webp";
        Storage::disk('public')->put($thumbWebp, $img->toWebp(75));

        $media->update([
            'original_path' => $originalWebp,
            'medium_path'   => $mediumWebp,
            'thumb_path'    => $thumbWebp,
        ]);

        Storage::disk('public')->delete($this->originalPath);
    }
}
