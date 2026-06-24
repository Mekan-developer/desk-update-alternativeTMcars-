<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RejectionReason;
use App\Models\Video;
use Illuminate\Http\Request;
use Inertia\Inertia;

class VideoController extends Controller
{
    public function index(Request $request)
    {
        $videos = Video::with('user')
            ->when($request->status, fn($q, $s) => $q->where('status', $s))
            ->when($request->search, fn($q, $s) => $q->where('title', 'like', "%$s%"))
            ->latest()
            ->paginate(25)
            ->withQueryString();

        return Inertia::render('Videos/Index', [
            'videos'           => $videos,
            'rejectionReasons' => RejectionReason::where('type', 'video')->where('is_active', true)->get(),
            'filters'          => $request->only('status', 'search'),
            'counts'           => [
                'pending'  => Video::where('status', 'pending')->count(),
                'approved' => Video::where('status', 'approved')->count(),
                'rejected' => Video::where('status', 'rejected')->count(),
            ],
        ]);
    }

    public function show(Video $video)
    {
        $video->load('user', 'rejectionReason');

        return Inertia::render('Videos/Show', [
            'video'            => $video,
            'rejectionReasons' => RejectionReason::where('type', 'video')->where('is_active', true)->get(),
        ]);
    }

    public function update(Request $request, Video $video)
    {
        $video->update($request->only('title', 'status'));

        return back()->with('toast', ['type' => 'success', 'message' => 'Обновлено']);
    }

    public function destroy(Request $request, Video $video)
    {
        if (! $request->user()->isAdmin()) {
            abort(403);
        }

        $video->delete();

        return redirect()->route('videos.index')
            ->with('toast', ['type' => 'success', 'message' => 'Ролик удалён']);
    }

    public function approve(Video $video)
    {
        $video->update(['status' => 'approved', 'rejection_reason_id' => null]);

        return back()->with('toast', ['type' => 'success', 'message' => 'Ролик одобрен']);
    }

    public function reject(Request $request, Video $video)
    {
        $data = $request->validate(['rejection_reason_id' => 'required|exists:rejection_reasons,id']);
        $video->update(['status' => 'rejected', 'rejection_reason_id' => $data['rejection_reason_id']]);

        return back()->with('toast', ['type' => 'error', 'message' => 'Ролик отклонён']);
    }
}
