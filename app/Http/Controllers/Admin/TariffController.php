<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tariff;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TariffController extends Controller
{
    public function index()
    {
        return Inertia::render('Tariffs/Index', [
            'tariffs' => Tariff::withCount('users')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'           => 'required|string|max:255',
            'listings_limit' => 'required|integer|min:0',
            'videos_limit'   => 'required|integer|min:0',
            'boosts_limit'   => 'required|integer|min:0',
            'duration_days'  => 'required|integer|min:1',
            'is_free'        => 'boolean',
            'is_active'      => 'boolean',
        ]);

        Tariff::create($data);

        return back()->with('toast', ['type' => 'success', 'message' => 'Тариф добавлен']);
    }

    public function update(Request $request, Tariff $tariff)
    {
        $data = $request->validate([
            'name'           => 'sometimes|string|max:255',
            'listings_limit' => 'sometimes|integer|min:0',
            'videos_limit'   => 'sometimes|integer|min:0',
            'boosts_limit'   => 'sometimes|integer|min:0',
            'duration_days'  => 'sometimes|integer|min:1',
            'is_free'        => 'boolean',
            'is_active'      => 'boolean',
        ]);

        $tariff->update($data);

        return back()->with('toast', ['type' => 'success', 'message' => 'Тариф обновлён']);
    }

    public function destroy(Request $request, Tariff $tariff)
    {
        if (! $request->user()->isAdmin()) {
            abort(403);
        }

        $tariff->delete();

        return back()->with('toast', ['type' => 'success', 'message' => 'Тариф удалён']);
    }

    public function toggle(Tariff $tariff)
    {
        $tariff->update(['is_active' => ! $tariff->is_active]);

        return back()->with('toast', ['type' => 'success', 'message' => 'Обновлено']);
    }
}
