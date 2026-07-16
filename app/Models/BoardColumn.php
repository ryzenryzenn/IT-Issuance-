<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * A user-defined column (status) on the Kanban board.
 */
class BoardColumn extends Model
{
    protected $fillable = ['name', 'key', 'color', 'position'];

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('position')->orderBy('id');
    }

    /** Tickets that currently sit in this column (matched by status key). */
    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'status', 'key');
    }
}
