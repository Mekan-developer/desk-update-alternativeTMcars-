<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\District;
use App\Models\Region;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RegionController extends Controller
{
    public function index()
    {
        return Inertia::render('Regions/Index', [
            'regions' => Region::with([
                'cities' => fn ($q) => $q->orderBy('name_ru')->with([
                    'districts' => fn ($q) => $q->orderBy('name_ru'),
                ]),
            ])->orderBy('name_ru')->get(),
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

    public function toggle(Region $region)
    {
        $hide = ! $region->is_hidden;
        $region->update(['is_hidden' => $hide]);

        // Скрытие региона каскадно скрывает вложенные города и районы.
        if ($hide) {
            $cityIds = $region->cities()->pluck('id');
            $region->cities()->update(['is_hidden' => true]);
            District::whereIn('city_id', $cityIds)->update(['is_hidden' => true]);
        }

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
