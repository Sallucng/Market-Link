<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AdminProductRequest extends FormRequest
{
    public function authorize()
    {
        // Admin middleware already ensures proper auth
        return true;
    }

    public function rules()
    {
        return [
            'name'               => ['required', 'string', 'max:255'],
            'description'        => ['nullable', 'string'],
            'price'              => ['required', 'numeric', 'min:0'],
            'stock'              => ['required', 'integer', 'min:0'],
            'category_id'        => ['required', 'exists:categories,id'],
            'farmer_profile_id' => ['required', 'exists:farmer_profiles,id'],
            'image'              => ['nullable', 'image', 'max:2048'],
            'is_available'       => ['sometimes', 'boolean'],
            'is_moderated'       => ['sometimes', 'boolean'],
        ];
    }
}
