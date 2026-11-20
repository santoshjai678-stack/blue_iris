<?php

namespace App\Http\Requests\Tenant;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'question'              => ['required', 'string'],
            'task_categorys_id'     => ['required', 'exists:task_categorys,id'],
            'salon_id'              => ['nullable', 'exists:salons,id'],
            'designations_id'       => ['required', 'exists:designations,id'],
            'survey_periods_id'     => ['required', 'exists:survey_periods,id'],
            'task_type'             => ['required', 'in:general,expiry'],
            'task_tag_slug'         => ['required', 'in:general,cash,attrition,expiry'],
            'control_type'          => ['required', 'in:1,2,3'],
            'is_question_mandatory' => ['nullable', 'boolean'],
            'is_other_option_add'   => ['nullable', 'boolean'],
            'slot_times'            => ['nullable', 'array'],
            'slot_times.*'          => ['exists:survey_slot_times,id'],
            'options'               => ['nullable', 'array'],
            'options.*'             => ['nullable', 'string', 'max:255'],
            'status'                => ['required', 'in:0,1'],
        ];
    }
}