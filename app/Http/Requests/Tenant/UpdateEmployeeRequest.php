<?php

namespace App\Http\Requests\Tenant;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $employee = $this->route('employee');
        $empId = is_object($employee) ? $employee->id : $employee;

        return [
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150', 'unique:employees,email,' . $empId],
            'phone_no' => ['nullable', 'string', 'max:20'],
            'dob' => ['nullable', 'date'],
            'joining_date' => ['nullable', 'date'],
            'rejoin_date' => ['nullable', 'date'],
            'address' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'zipcode' => ['nullable', 'string', 'max:20'],
            'salon_id' => ['required', 'exists:salons,id'],
            'status' => ['required', 'in:0,1,2'],
            'termination_reason' => ['nullable', 'string', 'max:255'],
            'start_time' => ['nullable', 'string', 'max:10'],
            'end_time' => ['nullable', 'string', 'max:10'],
        ];
    }

    public function messages(): array
    {
        return [
            'salon_id.required' => 'Please select an assigned store for this employee.',
            'salon_id.exists' => 'The selected store is invalid.',
        ];
    }
}
