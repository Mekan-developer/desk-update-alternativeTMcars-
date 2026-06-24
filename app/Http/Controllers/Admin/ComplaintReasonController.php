<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ComplaintReason;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ComplaintReasonController extends Controller
{
    public function index()
    {
        return Inertia::render('ComplaintReasons/Index', [
            'reasons' => ComplaintReason::all(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name_ru'   => 'required|string',
            'name_tk'   => 'required|string',
            'is_active' => 'boolean',
        ]);

        ComplaintReason::create($data);

        return back()->with('toast', ['type' => 'success', 'message' => 'Причина добавлена']);
    }

    public function update(Request $request, ComplaintReason $complaintReason)
    {
        $complaintReason->update($request->only('name_ru', 'name_tk', 'is_active'));

        return back()->with('toast', ['type' => 'success', 'message' => 'Обновлено']);
    }

    public function destroy(ComplaintReason $complaintReason)
    {
        $complaintReason->delete();

        return back()->with('toast', ['type' => 'success', 'message' => 'Удалено']);
    }
}
