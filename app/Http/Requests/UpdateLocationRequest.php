<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateLocationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update locations');
    }

    public function rules(): array
    {
        $id = $this->route('location');

        return [
            'name'        => ['required', 'string', 'max:255', Rule::unique('locations', 'name')->ignore($id)],
            'description' => ['nullable', 'string', 'max:255'],
        ];
    }
}
