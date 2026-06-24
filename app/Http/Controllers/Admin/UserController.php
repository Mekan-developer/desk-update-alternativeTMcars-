<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Region;
use App\Models\Tariff;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::with('region', 'city', 'tariff')
            ->where('role', 'user')
            ->when($request->search, fn($q, $s) => $q->where('phone', 'like', "%$s%")->orWhere('name', 'like', "%$s%"))
            ->when($request->status, fn($q, $s) => $q->where('status', $s))
            ->when($request->region_id, fn($q, $r) => $q->where('region_id', $r))
            ->latest()
            ->paginate(25)
            ->withQueryString();

        return Inertia::render('Users/Index', [
            'users'   => $users,
            'regions' => Region::all(),
            'tariffs' => Tariff::where('is_active', true)->get(),
            'filters' => $request->only('search', 'status', 'region_id'),
        ]);
    }

    public function store(Request $request)
    {
        if ($request->role !== 'user' && ! $request->user()->isAdmin()) {
            abort(403);
        }

        $data = $request->validate([
            'name'      => 'required|string|max:255',
            'phone'     => 'required|string|unique:users,phone',
            'password'  => 'required|string|min:6|confirmed',
            'role'      => 'required|in:admin,manager,user',
            'gender'    => 'nullable|in:male,female',
            'region_id' => 'nullable|exists:regions,id',
            'city_id'   => 'nullable|exists:cities,id',
        ]);

        $data['password'] = Hash::make($data['password']);

        User::create($data);

        return back()->with('toast', ['type' => 'success', 'message' => 'Пользователь создан']);
    }

    public function show(User $user)
    {
        $user->load('region', 'city', 'tariff');

        $userListings = $user->listings()->with('category', 'region')->latest()->take(10)->get();

        return Inertia::render('Users/Show', [
            'user'         => $user,
            'userListings' => $userListings,
            'stats'        => [
                'listings'   => $user->listings()->count(),
                'videos'     => $user->videos()->count(),
                'complaints' => \App\Models\Complaint::where('user_id', $user->id)->count(),
            ],
        ]);
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name'      => 'sometimes|string|max:255',
            'note'      => 'nullable|string',
            'region_id' => 'nullable|exists:regions,id',
            'city_id'   => 'nullable|exists:cities,id',
        ]);

        $user->update($data);

        return back()->with('toast', ['type' => 'success', 'message' => 'Сохранено']);
    }

    public function destroy(Request $request, User $user)
    {
        if (! $request->user()->isAdmin()) {
            abort(403, 'Только администратор может удалять пользователей');
        }

        $user->delete();

        return redirect()->route('users.index')
            ->with('toast', ['type' => 'success', 'message' => 'Пользователь удалён']);
    }

    public function block(Request $request, User $user)
    {
        $data = $request->validate(['reason' => 'nullable|string']);
        $user->update(['status' => 'blocked', 'blocked_reason' => $data['reason'] ?? null]);

        return back()->with('toast', ['type' => 'success', 'message' => 'Пользователь заблокирован']);
    }

    public function unblock(User $user)
    {
        $user->update(['status' => 'active', 'blocked_reason' => null]);

        return back()->with('toast', ['type' => 'success', 'message' => 'Пользователь разблокирован']);
    }

    public function assignTariff(Request $request, User $user)
    {
        $data = $request->validate([
            'tariff_id' => 'required|exists:tariffs,id',
        ]);

        $tariff = Tariff::findOrFail($data['tariff_id']);
        $user->update([
            'tariff_id'      => $tariff->id,
            'tariff_ends_at' => now()->addDays($tariff->duration_days),
        ]);

        return back()->with('toast', ['type' => 'success', 'message' => 'Тариф назначен']);
    }
}
