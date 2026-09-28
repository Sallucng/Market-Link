<?php

namespace App\Http\Requests\Farmer;

use Illuminate\Foundation\Http\FormRequest;

class OrderStatusUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'status' => ['required', 'string', 'in:accepted,declined,ready_for_pickup'],
            'decline_reason' => [
                'required_if:status,declined',
                'nullable',
                'string',
                'max:500',
            ],
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
            'status.in' => 'Order status can only be transitioned to accepted, declined, or ready_for_pickup.',
            'decline_reason.required_if' => 'A reason is mandatory when declining a customer order.',
            'decline_reason.max' => 'The decline reason cannot exceed 500 characters.',
        ];
    }
}
