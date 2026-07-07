<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Шаг 2 смены номера: подтверждение SMS-кода, пришедшего на новый номер.
 */
class ConfirmPhoneRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'phone' => ['required', 'string', 'regex:/^\+?\d{8,15}$/'],
            'code'  => ['required', 'string'],
        ];
    }
}
