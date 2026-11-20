<?php

namespace App\Http\Requests\Tenant;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSystemUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->route('system_user');
        if (is_object($userId)) {
            $userId = $userId->id;
        }

        return [
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email,' . $userId],
            'phone_no' => ['nullable', 'string', 'max:20'],
            'role' => ['required_without:roles', 'nullable', 'string', 'exists:roles,name'],
            'roles' => ['nullable'],
            'password' => ['nullable', 'string', 'min:6'],
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
