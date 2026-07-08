<?php

namespace App\Services;

use App\Models\Banner;
use App\Repositories\Interfaces\BannerRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class BannerService
{
    /** Баннер: 2:1, ширина под мобильные, вес ≤ 150 КБ */
    private const IMAGE_ASPECT    = 2 / 1;
    private const IMAGE_MAX_WIDTH = 1200;
    private const IMAGE_MAX_BYTES = 150 * 1024;

    public function __construct(
        private readonly BannerRepositoryInterface $banners,
        private readonly ImageConversionService $imageConversion,
    ) {}

    public function list(array $filters): LengthAwarePaginator
    {
        return $this->banners->paginate($filters);
    }

    public function activeForApi(): Collection
    {
        return $this->banners->activeForApi();
    }

    public function store(array $data, UploadedFile $image, array $crop = []): Banner
    {
        $data['image'] = $this->storeImage($image, $crop);

        return $this->banners->create($data);
    }

    public function update(Banner $banner, array $data, ?UploadedFile $image = null, array $crop = []): Banner
    {
        if ($image) {
            Storage::disk('public')->delete($banner->image);
            $data['image'] = $this->storeImage($image, $crop);
        }

        return $this->banners->update($banner, $data);
    }

    private function storeImage(UploadedFile $image, array $crop): string
    {
        return $this->imageConversion->toWebp(
            $image,
            'banners',
            aspect: self::IMAGE_ASPECT,
            cropX: (float) ($crop['crop_x'] ?? 50),
            cropY: (float) ($crop['crop_y'] ?? 50),
            maxWidth: self::IMAGE_MAX_WIDTH,
            maxBytes: self::IMAGE_MAX_BYTES,
        );
    }

    public function toggleActive(Banner $banner): void
    {
        $this->banners->update($banner, ['is_active' => ! $banner->is_active]);
    }

    public function move(Banner $banner, string $direction): void
    {
        $ordered = $this->banners->ordered()->values();
        $index = $ordered->search(fn ($b) => $b->id === $banner->id);
        $targetIndex = $direction === 'up' ? $index - 1 : $index + 1;

        if ($index === false || ! $ordered->has($targetIndex)) {
            return;
        }

        $target = $ordered[$targetIndex];
        $order = $banner->sort_order;

        $this->banners->update($banner, ['sort_order' => $target->sort_order]);
        $this->banners->update($target, ['sort_order' => $order]);
    }

    public function delete(Banner $banner): void
    {
        Storage::disk('public')->delete($banner->image);
        $this->banners->delete($banner);
    }
}
