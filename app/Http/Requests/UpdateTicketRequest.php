<?php

namespace App\Http\Requests;

use App\Enums\Permission;
use App\Models\Ticket;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can(Permission::UpdateTickets->value);
    }

    public function rules(): array
    {
        return [
            'title'       => ['required', 'string', 'max:255'],
            'body'        => ['nullable', 'string'],
            'type'        => ['required', Rule::in(Ticket::TYPES)],
            'status'      => ['required', 'exists:board_columns,key'],
            'priority'    => ['required', Rule::in(['low', 'normal', 'high'])],
            'color'       => ['required', Rule::in(['yellow', 'blue', 'green', 'pink', 'purple'])],
            'asset_id'    => ['nullable', 'exists:assets,id'],
            'employee_id' => ['nullable', 'exists:employees,id'],
            'due_date'    => ['nullable', 'date'],
        ];
    }
}
