<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class StoreReviewRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'text'   => ['required', 'string', 'max:2000'],
            'rating' => ['nullable', 'integer', 'between:1,5'],

            // Отзыв связан либо с объявлением, либо с пользователем — ровно один из двух (ТЗ 8.2)
            'listing_id' => [
                'nullable', 'integer', 'exists:listings,id',
                'required_without:target_user_id', 'prohibits:target_user_id',
            ],
            'target_user_id' => [
                'nullable', 'integer', 'exists:users,id',
                'required_without:listing_id', 'prohibits:listing_id',
            ],
        ];
    }
}
