<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\City;
use Illuminate\Http\Request;

class CityController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'name_ru'   => 'required|string',
            'name_tk'   => 'required|string',
            'region_id' => 'required|exists:regions,id',
        ]);
        City::create($data);

        return back()->with('toast', ['type' => 'success', 'message' => 'Город добавлен']);
    }

    public function update(Request $request, City $city)
    {
        $data = $request->validate([
            'name_ru'   => 'required|string',
            'name_tk'   => 'required|string',
            'region_id' => 'required|exists:regions,id',
        ]);
        $city->update($data);

        return back()->with('toast', ['type' => 'success', 'message' => 'Обновлено']);
    }

    public function toggle(City $city)
    {
        $hide = ! $city->is_hidden;
        $city->update(['is_hidden' => $hide]);

        // Скрытие города каскадно скрывает его районы.
        if ($hide) {
            $city->districts()->update(['is_hidden' => true]);
        }

        return back()->with('toast', ['type' => 'success', 'message' => 'Обновлено']);
    }

    public function destroy(Request $request, City $city)
    {
        if (! $request->user()->isAdmin()) {
            abort(403);
        }

        $city->delete();

        return back()->with('toast', ['type' => 'success', 'message' => 'Город удалён']);
    }
}
