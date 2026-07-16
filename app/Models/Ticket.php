<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * A sticky note on the Kanban board — either a technical-support ticket
 * or a temporarily issued asset that needs to come back.
 */
class Ticket extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    public const STATUSES = ['todo', 'in_progress', 'done'];
    public const TYPES    = ['support', 'temp_issue', 'deployment'];

    protected $fillable = [
        'title', 'body', 'type', 'status', 'priority', 'color',
        'asset_id', 'employee_id', 'created_by_user_id', 'due_date', 'position',
    ];

    protected function casts(): array
    {
        return ['due_date' => 'date'];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['title', 'type', 'status', 'priority', 'asset_id', 'employee_id', 'due_date'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('ticket');
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('position')->orderBy('id');
    }

    /** Is this a temporarily issued asset that is still out? */
    public function isOverdue(): bool
    {
        return $this->due_date !== null
            && $this->status !== 'done'
            && $this->due_date->isPast();
    }
}
