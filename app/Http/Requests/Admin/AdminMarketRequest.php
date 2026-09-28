<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class AdminMarketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $isUpdate = $this->isMethod('put') || $this->isMethod('patch');

        return [
            'name'           => [$isUpdate ? 'sometimes' : 'required', 'string', 'max:255'],
            'location'       => ['nullable', 'string', 'max:255'],
            'address'        => [$isUpdate ? 'sometimes' : 'required', 'string', 'max:255'],
            'latitude'       => ['nullable', 'numeric'],
            'longitude'      => ['nullable', 'numeric'],
            'operating_days' => [$isUpdate ? 'sometimes' : 'required'],
            'open_time'      => ['nullable', 'string'],
            'close_time'     => ['nullable', 'string'],
            'opening_time'   => ['nullable', 'string'],
            'closing_time'   => ['nullable', 'string'],
            'city'           => ['nullable', 'string', 'max:255'],
            'state'          => ['nullable', 'string', 'max:255'],
            'postal_code'    => ['nullable', 'string', 'max:50'],
            'description'    => ['nullable', 'string'],
            'status'         => ['nullable', 'in:active,inactive'],
            'image'          => ['nullable'],
        ];
    }
}
