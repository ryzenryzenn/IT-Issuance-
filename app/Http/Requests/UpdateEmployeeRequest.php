<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update employees');
    }

    public function rules(): array
    {
        $id = $this->route('employee');

        return [
            'name'        => ['required', 'string', 'max:255'],
            'employee_no' => ['nullable', 'string', 'max:100', Rule::unique('employees', 'employee_no')->ignore($id)],
            'department'  => ['nullable', 'string', 'max:255'],
            'position'    => ['nullable', 'string', 'max:255'],
            'email'       => ['nullable', 'email', 'max:255'],
            'is_active'   => ['required', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['is_active' => $this->boolean('is_active')]);
    }
}
