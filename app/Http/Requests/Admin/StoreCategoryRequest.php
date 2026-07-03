<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreCategoryRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name_ru'     => 'required|string|max:255',
            'name_tk'     => 'nullable|string|max:255',
            'parent_id'   => 'nullable|integer|exists:categories,id',
            'is_active'   => 'boolean',
            'icon'        => 'nullable|file|mimes:svg|max:1024',
            'icon_path'   => 'nullable|string|exists:category_icons,path',
        ];
    }
}
