<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\ComplaintReason;
use App\Repositories\Interfaces\ComplaintRepositoryInterface;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ComplaintController extends Controller
{
    public function __construct(
        private readonly ComplaintRepositoryInterface $complaintRepository,
    ) {}

    public function index(Request $request)
    {
        return Inertia::render('Complaints/Index', [
            'complaints' => $this->complaintRepository->paginate($request->only('status')),
            'reasons'    => ComplaintReason::where('is_active', true)->get(),
            'filters'    => $request->only('status'),
            'counts'     => [
                'pending'  => $this->complaintRepository->countPending(),
                'resolved' => Complaint::where('status', 'resolved')->count(),
            ],
        ]);
    }

    public function resolve(Request $request, Complaint $complaint)
    {
        $data = $request->validate([
            'status' => 'required|in:resolved',
        ]);

        $this->complaintRepository->update($complaint, $data);

        return back()->with('toast', ['type' => 'success', 'message' => __('messages.updated')]);
    }
}
