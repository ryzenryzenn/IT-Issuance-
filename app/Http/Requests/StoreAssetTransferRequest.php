<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;

class StoreAssetTransferRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('transfer assets');
    }

    protected function prepareForValidation(): void
    {
        [$type, $id] = array_pad(explode(':', (string) $this->input('to_assignee')), 2, null);

        $this->merge([
            'to_assignee_type' => in_array($type, ['employee', 'location'], true) ? $type : null,
            'to_assignee_id'   => is_numeric($id) ? (int) $id : null,
        ]);
    }

    public function rules(): array
    {
        return [
            'to_assignee_type' => ['required', 'in:employee,location'],
            'to_assignee_id'   => ['required', 'integer', function ($attr, $value, $fail) {
                $table = $this->to_assignee_type === 'location' ? 'locations' : 'employees';
                if (! DB::table($table)->where('id', $value)->exists()) {
                    $fail('The selected assignee is invalid.');
                }
            }],
            'to_location_id'   => ['nullable', 'exists:locations,id'],
            'notes'            => ['nullable', 'string'],
            'transferred_at'   => ['required', 'date'],
        ];
    }
}
