<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Локаль мобильного API: заголовок Accept-Language (tk|ru), по умолчанию ru.
 */
class SetApiLocale
{
    private const SUPPORTED = ['tk', 'ru'];
    private const DEFAULT = 'ru';

    public function handle(Request $request, Closure $next): Response
    {
        $locale = strtolower(substr((string) $request->header('Accept-Language'), 0, 2));

        app()->setLocale(in_array($locale, self::SUPPORTED, true) ? $locale : self::DEFAULT);

        return $next($request);
    }
}
