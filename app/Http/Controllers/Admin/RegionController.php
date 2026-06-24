<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Region;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RegionController extends Controller
{
    public function index()
    {
        return Inertia::render('Regions/Index', [
            'regions' => Region::withCount('cities')->get(),
            'cities'  => City::with('region')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate(['name_ru' => 'required|string', 'name_tk' => 'required|string']);
        Region::create($data);

        return back()->with('toast', ['type' => 'success', 'message' => 'Регион добавлен']);
    }

    public function update(Request $request, Region $region)
    {
        $data = $request->validate(['name_ru' => 'required|string', 'name_tk' => 'required|string']);
        $region->update($data);

        return back()->with('toast', ['type' => 'success', 'message' => 'Обновлено']);
    }

    public function destroy(Request $request, Region $region)
    {
        if (! $request->user()->isAdmin()) {
            abort(403);
        }

        $region->delete();

        return back()->with('toast', ['type' => 'success', 'message' => 'Регион удалён']);
    }
}
