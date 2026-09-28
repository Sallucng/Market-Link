<?php

namespace App\Http\Requests\Farmer;

use Illuminate\Foundation\Http\FormRequest;

class FarmerRegisterRequest extends FormRequest
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
            // User account credentials
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],

            // Farmer business details
            'business_name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'stall_number' => ['nullable', 'string', 'max:50'],
            'address' => ['required', 'string', 'max:255'],

            // Geographic coordinates
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],

            // Operating days and pickup schedule
            'operating_days' => ['required', 'array', 'min:1'],
            'operating_days.*' => ['string', 'in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday'],
            'pickup_start_time' => ['nullable', 'date_format:H:i'],
            'pickup_end_time' => ['nullable', 'date_format:H:i', 'after:pickup_start_time'],
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
            'email.unique' => 'An account with this email address already exists.',
            'password.confirmed' => 'The password confirmation does not match.',
            'operating_days.required' => 'Please select at least one operating day for your farm.',
            'operating_days.*.in' => 'Each operating day must be a valid day of the week.',
            'pickup_end_time.after' => 'The pickup end time must be later than the pickup start time.',
            'latitude.between' => 'Latitude must be a valid geographic coordinate between -90 and 90 degrees.',
            'longitude.between' => 'Longitude must be a valid geographic coordinate between -180 and 180 degrees.',
        ];
    }
}
