<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreReviewRequest;
use App\Http\Resources\Api\V1\ReviewResource;
use App\Services\ReviewService;

class ReviewController extends Controller
{
    public function __construct(
        private readonly ReviewService $reviewService,
    ) {}

    /**
     * Оставить отзыв (об объявлении или о пользователе) — уходит на модерацию.
     * POST /api/v1/reviews
     *
     * @authenticated
     */
    public function store(StoreReviewRequest $request)
    {
        $review = $this->reviewService->createFromApi($request->user(), $request->validated());

        return response()->json([
            'data'    => new ReviewResource($review),
            'message' => __('messages.review_submitted'),
        ], 201);
    }
}
