<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAnnouncementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->role === 'admin';
    }

    public function rules(): array
    {
        return [
            'title' => [
                'sometimes',
                'string',
                'max:255',
            ],

            'message' => [
                'sometimes',
                'string',
                'max:1000',
            ],

            'content' => [
                'sometimes',
                'string',
                'max:1000',
            ],

            'is_active' => [
                'sometimes',
                'boolean',
            ],

            'published_at' => [
                'sometimes',
                'nullable',
                'date',
            ],

            'target_role' => [
                'sometimes',
                'string',
                'max:50',
            ],

            'target_audience' => [
                'sometimes',
                'string',
                'max:50',
            ],

            'badge_type' => [
                'sometimes',
                'string',
                'max:50',
            ],

            'expires_at' => [
                'sometimes',
                'nullable',
                'date',
            ],
        ];
    }
}
