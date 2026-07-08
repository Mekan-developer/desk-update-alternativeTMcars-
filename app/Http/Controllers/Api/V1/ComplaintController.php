<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreComplaintRequest;
use App\Http\Resources\Api\V1\ComplaintResource;
use App\Services\ComplaintService;

class ComplaintController extends Controller
{
    public function __construct(
        private readonly ComplaintService $complaintService,
    ) {}

    /**
     * Пожаловаться на объявление — уходит в админку на обработку.
     * POST /api/v1/complaints
     *
     * @authenticated
     */
    public function store(StoreComplaintRequest $request)
    {
        $complaint = $this->complaintService->createFromApi($request->user(), $request->validated());

        return response()->json([
            'data'    => new ComplaintResource($complaint),
            'message' => __('messages.complaint_submitted'),
        ], 201);
    }
}
