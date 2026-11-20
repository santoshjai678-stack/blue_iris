<?php

namespace App\Http\Requests\Tenant;

use Illuminate\Foundation\Http\FormRequest;

class StoreSystemUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email'],
            'phone_no' => ['nullable', 'string', 'max:20'],
            'role' => ['required_without:roles', 'nullable', 'string', 'exists:roles,name'],
            'roles' => ['nullable'],
            'password' => ['required', 'string', 'min:6'],
            'status' => ['nullable', 'in:0,1'],
        ];
    }

    public function messages(): array
    {
        return [
            'role.required_without' => 'Please select a system portal role for this user.',
            'role.exists' => 'The selected role is invalid.',
        ];
    }
}
