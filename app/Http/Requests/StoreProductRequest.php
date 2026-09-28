<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
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
                'required',
                'integer',
                'exists:categories,id',
            ],

            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'description' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'price' => [
                'required',
                'numeric',
                'min:0.01',
            ],

            'unit' => [
                'required',
                'string',
                'max:50',
            ],

            'stock_quantity' => [
                'required',
                'integer',
                'min:0',
            ],

            'status' => [
                'nullable',
                'in:available,sold_out,unavailable',
            ],

            'image' => [
                'nullable',
                'string',
                'max:500',
            ],
        ];
    }
}