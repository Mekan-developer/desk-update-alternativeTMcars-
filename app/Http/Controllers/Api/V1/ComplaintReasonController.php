<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\ComplaintReasonResource;
use App\Services\ComplaintService;

class ComplaintReasonController extends Controller
{
    public function __construct(
        private readonly ComplaintService $complaintService,
    ) {}

    /**
     * Список активных причин жалобы — для выбора при подаче жалобы.
     * GET /api/v1/complaint-reasons
     */
    public function index()
    {
        return response()->json([
            'data' => ComplaintReasonResource::collection($this->complaintService->activeReasons()),
        ]);
    }
}
