<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsNotBlocked
{
    /**
     * Блокировка проверяется только при входе (AuthService::verify) — токен,
     * выданный до блокировки, продолжает работать. Этот middleware закрывает
     * публикацию контента для уже авторизованных заблокированных пользователей (ТЗ 13.3).
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()?->isBlocked()) {
            throw new AuthorizationException(__('messages.user_blocked_message'));
        }

        return $next($request);
    }
}
