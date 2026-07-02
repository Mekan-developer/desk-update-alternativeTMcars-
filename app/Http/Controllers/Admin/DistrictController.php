<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\District;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DistrictController extends Controller
{
    public function store(Request $request, City $city)
    {
        $data = $request->validate([
            'name_ru' => ['required', 'string', 'max:255', Rule::unique('districts')->where('city_id', $city->id)],
            'name_tk' => ['required', 'string', 'max:255'],
        ]);
        $city->districts()->create($data);

        return back()->with('toast', ['type' => 'success', 'message' => 'Район добавлен']);
    }

    public function update(Request $request, District $district)
    {
        $data = $request->validate([
            'name_ru' => ['required', 'string', 'max:255', Rule::unique('districts')->where('city_id', $district->city_id)->ignore($district->id)],
            'name_tk' => ['required', 'string', 'max:255'],
        ]);
        $district->update($data);

        return back()->with('toast', ['type' => 'success', 'message' => 'Обновлено']);
    }

    public function toggle(District $district)
    {
        $district->update(['is_hidden' => ! $district->is_hidden]);

        return back()->with('toast', ['type' => 'success', 'message' => 'Обновлено']);
    }

    public function destroy(Request $request, District $district)
    {
        if (! $request->user()->isAdmin()) {
            abort(403);
        }

        $district->delete();

        return back()->with('toast', ['type' => 'success', 'message' => 'Район удалён']);
    }
}
