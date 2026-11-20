<?php

namespace App\Http\Requests\Tenant;

use Illuminate\Foundation\Http\FormRequest;

class StoreSalonRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'salon_name'   => ['required', 'string', 'max:255'],
            'salon_email'  => ['nullable', 'email', 'max:255'],
            'salon_phone'  => ['nullable', 'string', 'max:30'],
            'address_1'    => ['nullable', 'string', 'max:255'],
            'address_2'    => ['nullable', 'string', 'max:255'],
            'city'         => ['nullable', 'string', 'max:100'],
            'country_id'   => ['nullable', 'exists:countries,id'],
            'state_id'     => ['nullable', 'exists:states,id'],
            'zipcode'      => ['nullable', 'string', 'max:20'],
            'salon_lat'    => ['nullable', 'numeric', 'between:-90,90'],
            'salon_long'   => ['nullable', 'numeric', 'between:-180,180'],
            'status'       => ['required', 'in:0,1'],
            'timings'      => ['nullable', 'array'],
            'timings.*.day_name'   => ['required', 'string'],
            'timings.*.open_time'  => ['nullable', 'string'],
            'timings.*.close_time' => ['nullable', 'string'],
            'timings.*.is_closed'  => ['nullable', 'boolean'],
        ];
    }
}