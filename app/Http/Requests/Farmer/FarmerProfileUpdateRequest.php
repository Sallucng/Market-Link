<?php

namespace App\Http\Requests\Farmer;

use Illuminate\Foundation\Http\FormRequest;

class FarmerProfileUpdateRequest extends FormRequest
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
        if ($this->has('farm_name') && !$this->has('business_name')) {
            $merge['business_name'] = $this->input('farm_name');
        }
        if ($this->has('bio') && !$this->has('description')) {
            $merge['description'] = $this->input('bio');
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
            'business_name' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'stall_number' => ['nullable', 'string', 'max:50'],
            'address' => ['sometimes', 'required', 'string', 'max:255'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'operating_days' => ['nullable', 'array'],
            'operating_days.*' => ['string', 'in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday'],
            'pickup_start_time' => ['nullable', 'date_format:H:i'],
            'pickup_end_time' => ['nullable', 'date_format:H:i', 'after:pickup_start_time'],
            'market_ids' => ['nullable', 'array'],
            'market_ids.*' => ['integer', 'exists:markets,id'],
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
            'operating_days.*.in' => 'Selected operating days must be valid weekdays.',
            'pickup_end_time.after' => 'Pickup end time must be after the pickup start time.',
            'market_ids.*.exists' => 'One or more selected markets do not exist.',
        ];
    }
}
