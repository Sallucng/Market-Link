<?php

namespace App\Http\Requests\Farmer;

use Illuminate\Foundation\Http\FormRequest;

class ProductStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $merge = [];
        if ($this->has('weekly_stock') && !$this->has('weekly_quota')) {
            $merge['weekly_quota'] = $this->input('weekly_stock');
        } elseif ($this->has('weekly_quota') && !$this->has('weekly_stock')) {
            $merge['weekly_stock'] = $this->input('weekly_quota');
        }
        if (!empty($merge)) {
            $this->merge($merge);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'description' => ['nullable', 'string', 'max:2000'],
            'price' => ['required', 'numeric', 'min:0'],
            'unit' => ['required', 'string', 'max:50'], // e.g. kg, dozen, bunch, piece
            'stock_quantity' => ['required', 'integer', 'min:0'],
            'weekly_quota' => ['nullable', 'integer', 'min:0'],
            'weekly_stock' => ['nullable', 'integer', 'min:0'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'is_available' => ['nullable', 'boolean'],
        ];
    }

    /**
     * Custom validation messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'category_id.exists' => 'The selected product category does not exist.',
            'price.min' => 'Product price must be greater than or equal to 0.',
            'stock_quantity.min' => 'Stock quantity cannot be a negative value.',
            'image.max' => 'Product image size must not exceed 2 megabytes (2048 KB).',
            'image.mimes' => 'Product image must be an image in jpeg, png, jpg, or webp format.',
        ];
    }
}
