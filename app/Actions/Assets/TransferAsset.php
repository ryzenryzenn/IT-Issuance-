<?php

namespace App\Actions\Assets;

use App\Models\Asset;
use App\Models\AssetTransfer;
use App\Models\Employee;
use App\Models\Location;
use Illuminate\Support\Facades\DB;

/**
 * Reassign an asset to a new holder (an employee or a shared location),
 * optionally move its physical location, log the transfer, and reset
 * accountability to pending — all in one transaction.
 */
class TransferAsset
{
    /**
     * @param  array{to_assignee_type: string, to_assignee_id: int, to_location_id: ?int, notes: ?string, transferred_at: string}  $data
     */
    public function handle(Asset $asset, array $data, int $performedByUserId): AssetTransfer
    {
        return DB::transaction(function () use ($asset, $data, $performedByUserId) {
            $newAssignee = $data['to_assignee_type'] === 'location'
                ? Location::find($data['to_assignee_id'])
                : Employee::find($data['to_assignee_id']);

            $newLocationId   = $data['to_location_id'] ?? null ?: $asset->location_id;
            $newLocationName = $newLocationId ? optional(Location::find($newLocationId))->name : null;

            $transfer = AssetTransfer::create([
                'asset_id'               => $asset->id,
                'from_user'              => $asset->assignee?->name,
                'to_user'                => $newAssignee?->name,
                'from_location'          => $asset->location?->name,
                'to_location'            => $newLocationName,
                'transferred_by_user_id' => $performedByUserId,
                'notes'                  => $data['notes'] ?? null,
                'transferred_at'         => $data['transferred_at'],
            ]);

            $asset->update([
                'assignee_type'                   => $data['to_assignee_type'],
                'assignee_id'                     => $data['to_assignee_id'],
                'location_id'                     => $newLocationId,
                'accountability_signed'           => Asset::ACCOUNTABILITY_PENDING,
                'accountability_uploaded_snipeit' => Asset::ACCOUNTABILITY_PENDING,
            ]);

            return $transfer;
        });
    }
}
