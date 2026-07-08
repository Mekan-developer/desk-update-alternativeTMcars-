<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateManagerPermissionsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->isAdmin();
    }

    public function rules(): array
    {
        return [
            // Каждый тумблер шлёт PATCH только со своим полем — оба должны
            // оставаться независимо опциональными, иначе один тумблер будет
            // требовать поле другого.
            'can_manage_news'    => 'sometimes|boolean',
            'can_manage_banners' => 'sometimes|boolean',
        ];
    }
}
