<?php

namespace App\Http\Controllers\Admin;

use App\Actions\ToggleManagerNewsPermissionAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateLocalizationRequest;
use App\Http\Requests\Admin\UpdateManagerPermissionsRequest;
use App\Models\ComplaintReason;
use App\Models\RejectionReason;
use App\Models\Setting;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Services\MonitoringService;
use App\Services\Sms\SmsGatewayStatusService;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class SettingsController extends Controller
{
    public function index(MonitoringService $monitoring, SmsGatewayStatusService $smsStatus)
    {
        return Inertia::render('Settings/Index', [
            'monitoring'         => $monitoring->getStatus(),
            'canManageNews'      => (bool) Setting::get('manager_can_manage_news', false),
            'rejectionReasons'   => RejectionReason::where('type', 'listing')->get(),
            'complaintReasons'   => ComplaintReason::all(),
            'ownLocale'          => Auth::user()->locale,
            'defaultAppLocale'   => Setting::get('default_app_locale', 'ru'),
            'smsStatus'          => $smsStatus->resolve(),
        ]);
    }

    public function updateManagerPermissions(UpdateManagerPermissionsRequest $request, ToggleManagerNewsPermissionAction $action)
    {
        $action->execute($request->boolean('can_manage_news'));

        return back()->with('toast', ['type' => 'success', 'message' => __('messages.manager_permission_updated')]);
    }

    public function updateLocalization(UpdateLocalizationRequest $request, UserRepositoryInterface $users)
    {
        $data = $request->validated();

        $users->updateLocale($request->user(), $data['own_locale'] ?? null);
        Setting::set('default_app_locale', $data['default_app_locale']);

        return back()->with('toast', ['type' => 'success', 'message' => __('messages.localization_updated')]);
    }
}
