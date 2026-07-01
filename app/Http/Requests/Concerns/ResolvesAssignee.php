<?php

namespace App\Http\Requests\Concerns;

use Illuminate\Support\Facades\DB;

/**
 * Parses a combined "assignee" select value ("employee:5" / "location:3")
 * into the polymorphic assignee_type / assignee_id fields and validates them.
 */
trait ResolvesAssignee
{
    protected function mergeAssignee(): void
    {
        [$type, $id] = array_pad(explode(':', (string) $this->input('assignee')), 2, null);

        $this->merge([
            'assignee_type' => in_array($type, ['employee', 'location'], true) ? $type : null,
            'assignee_id'   => is_numeric($id) ? (int) $id : null,
        ]);
    }

    protected function assigneeRules(): array
    {
        return [
            'assignee_type' => ['nullable', 'in:employee,location'],
            'assignee_id'   => ['nullable', 'required_with:assignee_type', 'integer', function ($attr, $value, $fail) {
                if (! $this->assignee_type) {
                    return;
                }
                $table = $this->assignee_type === 'employee' ? 'employees' : 'locations';
                if (! DB::table($table)->where('id', $value)->exists()) {
                    $fail('The selected assignee is invalid.');
                }
            }],
        ];
    }
}
