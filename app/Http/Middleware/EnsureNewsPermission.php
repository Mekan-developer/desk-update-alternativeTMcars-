<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureNewsPermission
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        $allowed = $user?->role === 'admin'
            || ($user?->role === 'manager' && (bool) Setting::get('manager_can_manage_news', false));

        if (! $allowed) {
            abort(403, 'Недостаточно прав');
        }

        return $next($request);
    }
}
