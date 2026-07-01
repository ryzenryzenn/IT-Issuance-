<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCompanyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create companies');
    }

    public function rules(): array
    {
        return [
            'name'    => ['required', 'string', 'max:255', 'unique:companies,name'],
            'code'    => ['required', 'string', 'max:20', 'unique:companies,code'],
            'address' => ['nullable', 'string', 'max:255'],
        ];
    }
}
