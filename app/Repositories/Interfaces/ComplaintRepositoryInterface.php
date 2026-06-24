<?php

namespace App\Repositories\Interfaces;

use App\Models\Complaint;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ComplaintRepositoryInterface
{
    public function paginate(array $filters, int $perPage = 25): LengthAwarePaginator;
    public function find(int $id): Complaint;
    public function update(Complaint $complaint, array $data): Complaint;
    public function countPending(): int;
}
