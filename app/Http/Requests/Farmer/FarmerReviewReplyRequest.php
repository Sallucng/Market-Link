<?php

namespace App\Http\Requests\Farmer;

use Illuminate\Foundation\Http\FormRequest;

class FarmerReviewReplyRequest extends FormRequest
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
            'farmer_reply' => ['required', 'string', 'min:2', 'max:1000'],
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
            'farmer_reply.required' => 'Please provide a response message before submitting.',
            'farmer_reply.min' => 'Your reply must be at least 2 characters long.',
            'farmer_reply.max' => 'Your reply cannot exceed 1000 characters.',
        ];
    }
}
