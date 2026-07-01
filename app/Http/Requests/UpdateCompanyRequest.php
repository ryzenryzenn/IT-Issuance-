<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCompanyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update companies');
    }

    public function rules(): array
    {
        $id = $this->route('company');

        return [
            'name'    => ['required', 'string', 'max:255', Rule::unique('companies', 'name')->ignore($id)],
            'code'    => ['required', 'string', 'max:20',  Rule::unique('companies', 'code')->ignore($id)],
            'address' => ['nullable', 'string', 'max:255'],
        ];
    }
}
