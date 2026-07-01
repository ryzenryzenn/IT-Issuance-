<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAssetTransferRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('transfer assets');
    }

    public function rules(): array
    {
        return [
            'to_user'        => ['required', 'string', 'max:255'],
            'to_location_id' => ['nullable', 'exists:locations,id'],
            'notes'          => ['nullable', 'string'],
            'transferred_at' => ['required', 'date'],
        ];
    }
}
