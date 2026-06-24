<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RejectionReason;
use App\Models\Review;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $reviews = Review::with('user', 'targetUser', 'listing', 'rejectionReason')
            ->when($request->status, fn($q, $s) => $q->where('status', $s))
            ->latest()
            ->paginate(25)
            ->withQueryString();

        return Inertia::render('Reviews/Index', [
            'reviews'          => $reviews,
            'rejectionReasons' => RejectionReason::where('type', 'review')->where('is_active', true)->get(),
            'filters'          => $request->only('status'),
            'counts'           => [
                'pending'  => Review::where('status', 'pending')->count(),
                'approved' => Review::where('status', 'approved')->count(),
                'rejected' => Review::where('status', 'rejected')->count(),
            ],
        ]);
    }

    public function approve(Review $review)
    {
        $review->update(['status' => 'approved', 'rejection_reason_id' => null]);

        return back()->with('toast', ['type' => 'success', 'message' => 'Отзыв одобрен']);
    }

    public function reject(Request $request, Review $review)
    {
        $data = $request->validate(['rejection_reason_id' => 'required|exists:rejection_reasons,id']);
        $review->update(['status' => 'rejected', 'rejection_reason_id' => $data['rejection_reason_id']]);

        return back()->with('toast', ['type' => 'error', 'message' => 'Отзыв отклонён']);
    }
}
