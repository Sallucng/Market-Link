<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReportRequest extends FormRequest
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
                'required',
                'string',
                'max:100',
            ],

            'report_date' => [
                'required',
                'date',
            ],

            'total_orders' => [
                'nullable',
                'integer',
                'min:0',
            ],

            'total_revenue' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'active_farmers' => [
                'nullable',
                'integer',
                'min:0',
            ],

            'description' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ];
    }
}
