<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreComplaintRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'listing_id'          => ['required', 'integer', 'exists:listings,id'],
            'complaint_reason_id' => ['required', 'integer', Rule::exists('complaint_reasons', 'id')->where('is_active', 1)],
            'text'                => ['nullable', 'string', 'max:2000'],
        ];
    }
}
