<?php

namespace App\Http\Controllers\Admin;

use App\Actions\AssignTariffAction;
use App\Actions\BlockUserAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AssignTariffRequest;
use App\Http\Requests\Admin\BlockUserRequest;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\Complaint;
use App\Models\Region;
use App\Models\Tariff;
use App\Models\User;
use App\Services\UserService;
use Inertia\Inertia;

class UserController extends Controller
{
    public function __construct(
        private readonly UserService $userService,
        private readonly BlockUserAction $blockAction,
        private readonly AssignTariffAction $assignTariffAction,
    ) {}

    public function index(\Illuminate\Http\Request $request)
    {
        return Inertia::render('Users/Index', [
            'users'   => $this->userService->list($request->only('search', 'status', 'region_id')),
            'regions' => Region::all(),
            'tariffs' => Tariff::where('is_active', true)->get(),
            'filters' => $request->only('search', 'status', 'region_id'),
        ]);
    }

    public function store(StoreUserRequest $request)
    {
        if ($request->validated('role') !== 'user' && ! $request->user()->isAdmin()) {
            abort(403);
        }

        $this->userService->store($request->validated());

        return back()->with('toast', ['type' => 'success', 'message' => __('messages.created')]);
    }

    public function show(User $user)
    {
        $user->load('region', 'city', 'tariff');

        return Inertia::render('Users/Show', [
            'user'         => $user,
            'userListings' => $user->listings()->with('category', 'region')->latest()->take(10)->get(),
            'stats'        => [
                'listings'   => $user->listings()->count(),
                'videos'     => $user->videos()->count(),
                'complaints' => Complaint::where('user_id', $user->id)->count(),
            ],
        ]);
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $this->userService->update($user, $request->validated());

        return back()->with('toast', ['type' => 'success', 'message' => __('messages.updated')]);
    }

    public function destroy(User $user)
    {
        abort_unless(request()->user()->isAdmin(), 403, __('messages.admin_only'));
        $this->userService->delete($user);

        return redirect()->route('users.index')
            ->with('toast', ['type' => 'success', 'message' => __('messages.deleted')]);
    }

    public function block(BlockUserRequest $request, User $user)
    {
        $this->blockAction->execute($user, $request->validated('reason'));

        return back()->with('toast', ['type' => 'success', 'message' => __('messages.user_blocked')]);
    }

    public function unblock(User $user)
    {
        $this->userService->unblock($user);

        return back()->with('toast', ['type' => 'success', 'message' => __('messages.user_unblocked')]);
    }

    public function assignTariff(AssignTariffRequest $request, User $user)
    {
        $tariff = Tariff::findOrFail($request->validated('tariff_id'));
        $this->assignTariffAction->execute($user, $tariff);

        return back()->with('toast', ['type' => 'success', 'message' => __('messages.tariff_assigned')]);
    }
}
