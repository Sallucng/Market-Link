<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAdminUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()
            && $this->user()->role === 'admin'
            && $this->user()->is_active;
    }

    public function rules(): array
    {
        return [
            'role' => [
                'sometimes',
                'in:customer,farmer,admin',
            ],

            'is_active' => [
                'sometimes',
                'boolean',
            ],
        ];
    }
}
