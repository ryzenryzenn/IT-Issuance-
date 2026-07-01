<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\ResolvesAssignee;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAssetRequest extends FormRequest
{
    use ResolvesAssignee;

    public function authorize(): bool
    {
        return $this->user()->can('create assets');
    }

    protected function prepareForValidation(): void
    {
        $this->mergeAssignee();
    }

    public function rules(): array
    {
        return [
            'company_id'                       => ['required', 'exists:companies,id'],
            'category_id'                      => ['required', 'exists:categories,id'],
            'asset_tag'                        => ['required', 'string', 'max:100', 'unique:assets,asset_tag'],
            'model_id'                         => ['required', 'exists:asset_models,id'],
            'serial_number'                    => ['nullable', 'string', 'max:255', Rule::unique('assets', 'serial_number')],
            'location_id'                      => ['nullable', 'exists:locations,id'],
            'rustdesk_id'                      => ['nullable', 'string', 'max:50'],
            'windows_license_key'              => ['nullable', 'string', 'max:255'],
            'latest_updates_remarks'           => ['nullable', 'string'],
            'accountability_signed'            => ['required', Rule::in(['yes', 'pending'])],
            'accountability_uploaded_snipeit'  => ['required', Rule::in(['yes', 'pending'])],
            'date_issued'                      => ['nullable', 'date'],
        ] + $this->assigneeRules();
    }
}
