<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class SearchListingsRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'search'      => ['nullable', 'string', 'max:100'],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'region_id'   => ['nullable', 'integer', 'exists:regions,id'],
            'city_id'     => ['nullable', 'integer', 'exists:cities,id'],
            'type'        => ['nullable', 'in:goods,services'],
            'price_min'   => ['nullable', 'numeric', 'min:0'],
            'price_max'   => ['nullable', 'numeric', 'min:0'],
            'sort'        => ['nullable', 'in:latest,price_asc,price_desc,nearest'],
            'lat'         => ['required_if:sort,nearest', 'nullable', 'numeric', 'between:-90,90'],
            'lng'         => ['required_if:sort,nearest', 'nullable', 'numeric', 'between:-180,180'],
            'limit'       => ['nullable', 'integer', 'between:1,50'],
            'page'        => ['nullable', 'integer', 'min:1'],
        ];
    }
}
