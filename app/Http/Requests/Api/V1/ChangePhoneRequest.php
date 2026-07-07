<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Шаг 1 смены номера: код отправляется на новый (обязательно свободный) номер.
 */
class ChangePhoneRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'phone' => ['required', 'string', 'regex:/^\+?\d{8,15}$/', 'unique:users,phone'],
        ];
    }
}
