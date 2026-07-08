<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBoostSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->isAdmin();
    }

    public function rules(): array
    {
        return [
            // От 1 часа до года — защита от нулевого/бессмысленного интервала.
            'boost_interval_hours' => 'required|integer|min:1|max:8760',
        ];
    }
}
