<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()
            && $this->user()->role === 'farmer'
            && $this->user()->is_active;
    }

    public function rules(): array
    {
        return [
            'category_id' => [
                'sometimes',
                'integer',
                'exists:categories,id',
            ],

            'name' => [
                'sometimes',
                'string',
                'max:255',
            ],

            'description' => [
                'sometimes',
                'nullable',
                'string',
                'max:1000',
            ],

            'price' => [
                'sometimes',
                'numeric',
                'min:0.01',
            ],

            'unit' => [
                'sometimes',
                'string',
                'max:50',
            ],

            'stock_quantity' => [
                'sometimes',
                'integer',
                'min:0',
            ],

            'status' => [
                'sometimes',
                'in:available,sold_out,unavailable',
            ],

            'image' => [
                'sometimes',
                'nullable',
                'string',
                'max:500',
            ],
        ];
    }
}