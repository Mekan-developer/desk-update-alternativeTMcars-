<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name'      => 'sometimes|string|max:255',
            'note'      => 'nullable|string|max:1000',
            'region_id' => 'nullable|exists:regions,id',
            'city_id'   => 'nullable|exists:cities,id',
        ];
    }
}
