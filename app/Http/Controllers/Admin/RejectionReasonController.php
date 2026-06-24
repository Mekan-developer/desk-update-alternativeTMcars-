<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RejectionReason;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RejectionReasonController extends Controller
{
    public function index()
    {
        return Inertia::render('RejectionReasons/Index', [
            'reasons' => RejectionReason::all(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name_ru'   => 'required|string',
            'name_tk'   => 'required|string',
            'type'      => 'required|in:listing,video,review',
            'is_active' => 'boolean',
        ]);

        RejectionReason::create($data);

        return back()->with('toast', ['type' => 'success', 'message' => 'Причина добавлена']);
    }

    public function update(Request $request, RejectionReason $rejectionReason)
    {
        $rejectionReason->update($request->only('name_ru', 'name_tk', 'type', 'is_active'));

        return back()->with('toast', ['type' => 'success', 'message' => 'Обновлено']);
    }

    public function destroy(RejectionReason $rejectionReason)
    {
        $rejectionReason->delete();

        return back()->with('toast', ['type' => 'success', 'message' => 'Удалено']);
    }
}
