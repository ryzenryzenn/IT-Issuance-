<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAssetModelRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update asset models');
    }

    public function rules(): array
    {
        $id = $this->route('assetModel');

        return [
            'name'        => ['required', 'string', 'max:255', Rule::unique('asset_models', 'name')->ignore($id)],
            'description' => ['nullable', 'string', 'max:255'],
        ];
    }
}
