<?php

namespace App\Actions;

use App\Jobs\ProcessListingImagesJob;
use App\Models\Listing;
use App\Repositories\Interfaces\ListingRepositoryInterface;
use Illuminate\Http\UploadedFile;

class AttachListingPhotosAction
{
    public function __construct(
        private readonly ListingRepositoryInterface $listingRepository,
    ) {}

    /**
     * Сохраняет загруженные фото во временную папку, создаёт записи listing_media
     * и ставит в очередь конвертацию в WebP (original/medium/thumb).
     *
     * @param UploadedFile[] $photos
     */
    public function execute(Listing $listing, array $photos): void
    {
        $order = $this->listingRepository->maxMediaOrder($listing) + 1;

        foreach ($photos as $photo) {
            $path = $photo->store("listings/{$listing->id}/photos/uploads", 'public');

            $media = $this->listingRepository->createMedia($listing, [
                'path'  => $path,
                'type'  => 'image',
                'order' => $order++,
            ]);

            ProcessListingImagesJob::dispatch($media->id, $path, $listing->id);
        }
    }
}
