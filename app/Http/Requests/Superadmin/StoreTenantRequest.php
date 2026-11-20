<?php

namespace App\Http\Requests\Superadmin;

use Illuminate\Foundation\Http\FormRequest;

class StoreTenantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tenant_id'    => 'required|string|min:3|max:50|alpha_dash|unique:tenants,id',
            'tenant_name'  => 'required|string|max:100',
            'subdomain'    => 'required|string|min:3|max:50|alpha_dash|unique:domains,domain',
            'email'        => 'required|email|max:100',
            'phone_no'     => 'required|string|max:20',
            'website_url'  => 'nullable|url|max:100',
            'address'      => 'nullable|string|max:255',
            'city'         => 'nullable|string|max:100',
            'state'        => 'nullable|string|max:100',
            'zipcode'      => 'nullable|string|max:20',
            'status'       => 'required|in:0,1',
        ];
    }
}