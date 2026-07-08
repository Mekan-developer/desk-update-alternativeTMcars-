<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateOwnLocaleRequest;
use App\Repositories\Interfaces\UserRepositoryInterface;

class LocaleController extends Controller
{
    public function update(UpdateOwnLocaleRequest $request, UserRepositoryInterface $users)
    {
        $users->updateLocale($request->user(), $request->validated()['locale']);

        return response()->noContent();
    }
}
