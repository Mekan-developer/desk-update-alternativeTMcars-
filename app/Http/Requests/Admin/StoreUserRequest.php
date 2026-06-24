<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name'      => 'required|string|max:255',
            'phone'     => 'required|string|unique:users,phone',
            'password'  => 'required|string|min:6|confirmed',
            'role'      => 'required|in:admin,manager,user',
            'gender'    => 'nullable|in:male,female',
            'region_id' => 'nullable|exists:regions,id',
            'city_id'   => 'nullable|exists:cities,id',
        ];
    }
}
