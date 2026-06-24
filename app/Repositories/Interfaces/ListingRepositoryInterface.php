<?php

namespace App\Repositories\Interfaces;

use App\Models\Listing;
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
}
