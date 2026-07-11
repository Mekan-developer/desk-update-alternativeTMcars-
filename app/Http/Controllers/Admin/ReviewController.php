<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RejectListingRequest;
use App\Models\RejectionReason;
use App\Models\Review;
use App\Repositories\Interfaces\ReviewRepositoryInterface;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ReviewController extends Controller
{
    public function __construct(
        private readonly ReviewRepositoryInterface $reviewRepository,
    ) {}

    public function index(Request $request)
    {
        return Inertia::render('Reviews/Index', [
            'reviews'          => $this->reviewRepository->paginate($request->only('status', 'search')),
            'rejectionReasons' => RejectionReason::where('type', 'review')->where('is_active', true)->get(),
            'filters'          => $request->only('status', 'search'),
            'counts'           => [
                'pending'  => $this->reviewRepository->countPending(),
                'approved' => $this->reviewRepository->countByStatus('approved'),
                'rejected' => $this->reviewRepository->countByStatus('rejected'),
            ],
        ]);
    }

    public function approve(Review $review)
    {
        $this->reviewRepository->update($review, ['status' => 'approved', 'rejection_reason_id' => null]);

        return back()->with('toast', ['type' => 'success', 'message' => __('messages.review_approved')]);
    }

    public function reject(RejectListingRequest $request, Review $review)
    {
        $this->reviewRepository->update($review, [
            'status'              => 'rejected',
            'rejection_reason_id' => $request->validated('rejection_reason_id'),
        ]);

        return back()->with('toast', ['type' => 'error', 'message' => __('messages.review_rejected')]);
    }
}
