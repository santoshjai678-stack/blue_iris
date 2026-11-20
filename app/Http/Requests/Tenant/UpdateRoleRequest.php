<?php

namespace App\Http\Requests\Tenant;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $roleId = $this->route('role');
        if (is_object($roleId)) {
            $roleId = $roleId->id;
        }

        return [
            'name' => ['required', 'string', 'max:50', 'unique:roles,name,' . $roleId],
            'permissions' => ['required', 'array', 'min:1'],
            'permissions.*' => ['exists:permissions,name'],
        ];
    }

    public function messages(): array
    {
        return [
            'permissions.required' => 'Please select at least one permission for this role.',
        ];
    }
}