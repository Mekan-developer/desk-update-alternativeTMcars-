<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Локаль админ-панели: берётся из users.locale текущего пользователя,
 * иначе остаётся дефолтная из конфига. Влияет на __() в валидации и тостах.
 */
class SetAdminLocale
{
    private const SUPPORTED = ['tk', 'ru'];

    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->user()?->locale;

        if (in_array($locale, self::SUPPORTED, true)) {
            app()->setLocale($locale);
        }

        return $next($request);
    }
}
