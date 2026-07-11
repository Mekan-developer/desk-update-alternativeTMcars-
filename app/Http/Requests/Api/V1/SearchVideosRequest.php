<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class SearchVideosRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:100'],
            'tag'    => ['nullable', 'string', 'max:30'],
            'limit'  => ['nullable', 'integer', 'min:1', 'max:50'],
            'page'   => ['nullable', 'integer', 'min:1'],
        ];
    }
}
