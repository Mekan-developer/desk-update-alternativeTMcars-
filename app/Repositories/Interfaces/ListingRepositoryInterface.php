<?php

namespace App\Repositories\Interfaces;

use App\Models\Listing;
use App\Models\ListingMedia;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface ListingRepositoryInterface
{
    public function paginate(array $filters, int $perPage = 25): LengthAwarePaginator;
    public function find(int $id): Listing;
    public function create(array $data): Listing;
    public function update(Listing $listing, array $data): Listing;
    public function delete(Listing $listing): void;
    public function countByStatus(string $status): int;
    public function countActiveByUser(int $userId): int;

    /** Публичная выдача для мобильного приложения: только approved + фильтры/сортировки */
    public function paginateForApi(array $filters, int $perPage = 20): LengthAwarePaginator;

    /** Объявления пользователя (любой статус) для экрана «Мои объявления» */
    public function paginateByUser(int $userId, array $filters, int $perPage = 20): LengthAwarePaginator;

    /** Занятая квота тарифа: объявления в перечисленных статусах */
    public function countByUserAndStatuses(int $userId, array $statuses): int;

    /** Занятая квота тарифа: сколько объявлений пользователя сейчас поднято */
    public function countBoostedByUser(int $userId): int;

    public function incrementViews(Listing $listing): void;

    public function createMedia(Listing $listing, array $attributes): ListingMedia;

    public function deleteMedia(ListingMedia $media): void;

    public function maxMediaOrder(Listing $listing): int;
}
