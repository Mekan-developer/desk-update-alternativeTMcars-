<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreNewsRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'title_ru'   => 'required|string|max:500',
            'title_tk'   => 'required|string|max:500',
            'content_ru' => 'required|string',
            'content_tk' => 'required|string',
            'type'       => 'required|in:news,advertisement',
            'link_type'  => 'nullable|in:user,listing',
            'link_id'    => 'nullable|integer',
            'image'      => 'nullable|image|max:5120',
            'is_published' => 'boolean',
        ];
    }
}
