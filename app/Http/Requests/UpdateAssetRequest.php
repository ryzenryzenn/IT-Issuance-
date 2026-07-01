<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAssetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update assets');
    }

    public function rules(): array
    {
        return [
            'company_id'                       => ['required', 'exists:companies,id'],
            'category_id'                      => ['required', 'exists:categories,id'],
            'asset_tag'                        => ['required', 'string', 'max:100', Rule::unique('assets', 'asset_tag')->ignore($this->route('asset'))],
            'model_id'                         => ['required', 'exists:asset_models,id'],
            'serial_number'                    => ['nullable', 'string', 'max:255'],
            'assigned_user'                    => ['nullable', 'string', 'max:255'],
            'location_id'                      => ['nullable', 'exists:locations,id'],
            'rustdesk_id'                      => ['nullable', 'string', 'max:50'],
            'windows_license_key'              => ['nullable', 'string', 'max:255'],
            'latest_updates_remarks'           => ['nullable', 'string'],
            'accountability_signed'            => ['required', Rule::in(['yes', 'pending'])],
            'accountability_uploaded_snipeit'  => ['required', Rule::in(['yes', 'pending'])],
            'date_issued'                      => ['nullable', 'date'],
        ];
    }
}
