<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAssetModelRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create asset models');
    }

    public function rules(): array
    {
        return [
            'name'        => ['required', 'string', 'max:255', 'unique:asset_models,name'],
            'description' => ['nullable', 'string', 'max:255'],
        ];
    }
}
