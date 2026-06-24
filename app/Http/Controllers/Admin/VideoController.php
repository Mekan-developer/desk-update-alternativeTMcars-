<?php

namespace App\Http\Controllers\Admin;

use App\Actions\ApproveVideoAction;
use App\Actions\RejectVideoAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RejectVideoRequest;
use App\Models\RejectionReason;
use App\Models\Video;
use App\Services\VideoService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class VideoController extends Controller
{
    public function __construct(
        private readonly VideoService $videoService,
        private readonly ApproveVideoAction $approveAction,
        private readonly RejectVideoAction $rejectAction,
    ) {}

    public function index(Request $request)
    {
        return Inertia::render('Videos/Index', [
            'videos'           => $this->videoService->list($request->only('status', 'search')),
            'rejectionReasons' => RejectionReason::where('type', 'video')->where('is_active', true)->get(),
            'filters'          => $request->only('status', 'search'),
            'counts'           => $this->videoService->counts(),
        ]);
    }

    public function show(Video $video)
    {
        return Inertia::render('Videos/Show', [
            'video'            => $video->load('user', 'rejectionReason'),
            'rejectionReasons' => RejectionReason::where('type', 'video')->where('is_active', true)->get(),
        ]);
    }

    public function update(Request $request, Video $video)
    {
        $video->update($request->only('title'));

        return back()->with('toast', ['type' => 'success', 'message' => __('messages.updated')]);
    }

    public function destroy(Video $video)
    {
        abort_unless(request()->user()->isAdmin(), 403);
        $this->videoService->delete($video);

        return redirect()->route('videos.index')
            ->with('toast', ['type' => 'success', 'message' => __('messages.deleted')]);
    }

    public function approve(Video $video)
    {
        $this->approveAction->execute($video);

        return back()->with('toast', ['type' => 'success', 'message' => __('messages.video_approved')]);
    }

    public function reject(RejectVideoRequest $request, Video $video)
    {
        $this->rejectAction->execute($video, $request->validated('rejection_reason_id'));

        return back()->with('toast', ['type' => 'error', 'message' => __('messages.video_rejected')]);
    }
}
