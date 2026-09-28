<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()
            && $this->user()->role === 'customer'
            && $this->user()->is_active;
    }

    public function rules(): array
    {
        return [
            'pickup_slot_id' => [
                'nullable',
                'integer',
                'exists:pickup_slots,id',
            ],

            'pickup_date' => [
                'nullable',
                'date',
                'after_or_equal:today',
            ],

            'pickup_time' => [
                'nullable',
                'date_format:H:i',
            ],

            'notes' => [
                'nullable',
                'string',
                'max:500',
            ],
        ];
    }
}