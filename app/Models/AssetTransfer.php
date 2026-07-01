<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class AssetTransfer extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'asset_id',
        'from_user',
        'to_user',
        'from_location',
        'to_location',
        'transferred_by_user_id',
        'notes',
        'transferred_at',
    ];

    protected function casts(): array
    {
        return [
            'transferred_at' => 'date',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['asset_id', 'from_user', 'to_user', 'from_location', 'to_location', 'transferred_at', 'notes'])
            ->dontSubmitEmptyLogs()
            ->useLogName('asset_transfer');
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function transferredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'transferred_by_user_id');
    }
}
