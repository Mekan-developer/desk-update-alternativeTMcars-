<?php

namespace App\Repositories;

use App\Models\Complaint;
use App\Repositories\Interfaces\ComplaintRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ComplaintRepository implements ComplaintRepositoryInterface
{
    public function paginate(array $filters, int $perPage = 25): LengthAwarePaginator
    {
        return Complaint::with('user', 'listing', 'complaintReason')
            ->when($filters['status'] ?? null, fn($q, $s) => $q->where('status', $s))
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
    }

    public function find(int $id): Complaint
    {
        return Complaint::with('user', 'listing', 'complaintReason')->findOrFail($id);
    }

    public function create(array $data): Complaint
    {
        return Complaint::create($data);
    }

    public function update(Complaint $complaint, array $data): Complaint
    {
        $complaint->update($data);
        return $complaint->fresh();
    }

    public function countPending(): int
    {
        return Complaint::where('status', 'new')->count();
    }
}
