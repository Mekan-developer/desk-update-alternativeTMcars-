<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\ComplaintReason;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ComplaintController extends Controller
{
    public function index(Request $request)
    {
        $complaints = Complaint::with('user', 'listing', 'complaintReason', 'resolver')
            ->when($request->status, fn($q, $s) => $q->where('status', $s))
            ->latest()
            ->paginate(25)
            ->withQueryString();

        return Inertia::render('Complaints/Index', [
            'complaints' => $complaints,
            'reasons'    => ComplaintReason::where('is_active', true)->get(),
            'filters'    => $request->only('status'),
            'counts'     => [
                'new'      => Complaint::where('status', 'new')->count(),
                'reviewed' => Complaint::where('status', 'reviewed')->count(),
                'resolved' => Complaint::where('status', 'resolved')->count(),
            ],
        ]);
    }

    public function resolve(Request $request, Complaint $complaint)
    {
        $data = $request->validate([
            'status'          => 'required|in:reviewed,resolved',
            'resolution_note' => 'nullable|string',
        ]);

        $complaint->update([
            'status'          => $data['status'],
            'resolution_note' => $data['resolution_note'] ?? null,
            'resolved_by'     => $request->user()->id,
        ]);

        return back()->with('toast', ['type' => 'success', 'message' => 'Жалоба обновлена']);
    }
}
