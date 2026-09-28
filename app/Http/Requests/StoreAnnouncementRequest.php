<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAnnouncementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->role === 'admin';
    }

    public function rules(): array
    {
        return [
            'title' => [
                'required',
                'string',
                'max:255',
            ],

            'message' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'content' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'is_active' => [
                'nullable',
                'boolean',
            ],

            'published_at' => [
                'nullable',
                'date',
            ],

            'target_role' => [
                'nullable',
                'string',
                'max:50',
            ],

            'target_audience' => [
                'nullable',
                'string',
                'max:50',
            ],

            'badge_type' => [
                'nullable',
                'string',
                'max:50',
            ],

            'expires_at' => [
                'nullable',
                'date',
            ],
        ];
    }
}
