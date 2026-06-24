<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreTariffRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name_ru'        => 'required|string|max:255',
            'name_tk'        => 'required|string|max:255',
            'listings_limit' => 'required|integer|min:0',
            'videos_limit'   => 'required|integer|min:0',
            'boost_limit'    => 'required|integer|min:0',
            'duration_days'  => 'required|integer|min:1',
            'is_active'      => 'boolean',
            'is_default'     => 'boolean',
        ];
    }
}
