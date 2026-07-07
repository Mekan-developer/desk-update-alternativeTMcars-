<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            // Телефон — единственный обязательный контакт: +993 и 8 цифр
            'phone'       => ['required', 'string', 'regex:/^\+993\d{8}$/', 'unique:users,phone'],
            // active — активен сразу; sms — код через локальный модем, активация при первом входе
            'activation'  => ['required', 'in:active,sms'],
            'region_id'   => ['required', 'exists:regions,id'],
            'city_id'     => ['nullable', Rule::exists('cities', 'id')->where('region_id', $this->input('region_id'))],
            'district_id' => ['nullable', Rule::exists('districts', 'id')->where('city_id', $this->input('city_id'))],
            'name'        => ['nullable', 'string', 'max:255'],
            'gender'      => ['nullable', 'in:male,female'],
            'birth_date'  => ['nullable', 'date', 'before:today'],
            'avatar'      => ['nullable', 'image', 'max:5120'],
        ];
    }

    public function messages(): array
    {
        return [
            'phone.unique' => __('messages.phone_already_registered'),
            'phone.regex'  => __('messages.phone_format_invalid'),
        ];
    }
}
