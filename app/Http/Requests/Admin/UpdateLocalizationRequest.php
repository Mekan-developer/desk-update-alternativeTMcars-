<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLocalizationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->isAdmin();
    }

    public function rules(): array
    {
        return [
            'own_locale'         => 'nullable|in:ru,tk',
            'default_app_locale' => 'required|in:ru,tk',
        ];
    }
}
