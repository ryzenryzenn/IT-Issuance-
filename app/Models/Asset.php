<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Asset extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    public const ACCOUNTABILITY_YES     = 'yes';
    public const ACCOUNTABILITY_PENDING = 'pending';

    protected $fillable = [
        'company_id',
        'category_id',
        'asset_tag',
        'model_id',
        'serial_number',
        'assigned_user',
        'location_id',
        'rustdesk_id',
        'windows_license_key',
        'latest_updates_remarks',
        'accountability_signed',
        'accountability_uploaded_snipeit',
        'date_issued',
    ];

    protected function casts(): array
    {
        return [
            'windows_license_key' => 'encrypted',
            'date_issued'         => 'date',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'company_id', 'category_id', 'asset_tag', 'model_id', 'serial_number',
                'assigned_user', 'location_id', 'rustdesk_id',
                'latest_updates_remarks',
                'accountability_signed', 'accountability_uploaded_snipeit',
                'date_issued',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('asset');
    }

    /**
     * Plain-text payload encoded into the asset's QR code.
     * Scanning with any phone camera shows the model, serial number,
     * and the assigned user.
     */
    public function qrPayload(): string
    {
        return implode("\n", [
            'Model: '.($this->model?->name ?: '—'),
            'Serial: '.($this->serial_number ?: 'N/A'),
            'Assignee: '.($this->assigned_user ?: 'Unassigned'),
        ]);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function model(): BelongsTo
    {
        return $this->belongsTo(AssetModel::class, 'model_id');
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function transfers(): HasMany
    {
        return $this->hasMany(AssetTransfer::class)->latest('transferred_at');
    }

    public function accountabilityFiles(): HasMany
    {
        return $this->hasMany(AccountabilityFile::class)->latest('created_at');
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (blank($term)) {
            return $query;
        }

        $like = '%'.$term.'%';

        return $query->where(function ($q) use ($like) {
            $q->where('asset_tag', 'like', $like)
                ->orWhere('serial_number', 'like', $like)
                ->orWhere('assigned_user', 'like', $like)
                ->orWhere('rustdesk_id', 'like', $like)
                ->orWhere('latest_updates_remarks', 'like', $like)
                ->orWhereHas('model', fn ($m) => $m->where('name', 'like', $like))
                ->orWhereHas('location', fn ($l) => $l->where('name', 'like', $like));
        });
    }

    public function scopePendingSignature(Builder $query): Builder
    {
        return $query->where('accountability_signed', self::ACCOUNTABILITY_PENDING);
    }

    public function scopePendingSnipeIt(Builder $query): Builder
    {
        return $query->where('accountability_uploaded_snipeit', self::ACCOUNTABILITY_PENDING);
    }

    public function scopeAssigned(Builder $query): Builder
    {
        return $query->whereNotNull('assigned_user')->where('assigned_user', '!=', '');
    }
}
