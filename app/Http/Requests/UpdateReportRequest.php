<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()
            && $this->user()->role === 'admin'
            && $this->user()->is_active;
    }

    public function rules(): array
    {
        return [
            'report_type' => [
                'sometimes',
                'string',
                'max:100',
            ],

            'report_date' => [
                'sometimes',
                'date',
            ],

            'total_orders' => [
                'sometimes',
                'integer',
                'min:0',
            ],

            'total_revenue' => [
                'sometimes',
                'numeric',
                'min:0',
            ],

            'active_farmers' => [
                'sometimes',
                'integer',
                'min:0',
            ],

            'description' => [
                'sometimes',
                'nullable',
                'string',
                'max:1000',
            ],
        ];
    }
}
