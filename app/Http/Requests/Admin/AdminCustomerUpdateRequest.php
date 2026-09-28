<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class AdminCustomerUpdateRequest extends FormRequest
{
    public function authorize()
    {
        // Admin middleware already ensures auth
        return true;
    }

    public function rules()
    {
        return [
            'name'  => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['sometimes', 'string', 'max:30'],
        ];
    }
}
