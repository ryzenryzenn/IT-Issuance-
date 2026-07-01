<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAssetTransferRequest;
use App\Models\Asset;
use App\Models\AssetTransfer;
use App\Models\Employee;
use App\Models\Location;
use Illuminate\Support\Facades\DB;

class AssetTransferController extends Controller
{
    public function store(StoreAssetTransferRequest $request, Asset $asset)
    {
        $this->authorize('transfer', $asset);

        DB::transaction(function () use ($request, $asset) {
            // Resolve the new assignee (an employee or a shared location).
            $newAssignee = $request->to_assignee_type === 'location'
                ? Location::find($request->to_assignee_id)
                : Employee::find($request->to_assignee_id);

            // New physical location: chosen one, or keep the asset's current location.
            $newLocationId   = $request->to_location_id ?: $asset->location_id;
            $newLocationName = $newLocationId ? optional(Location::find($newLocationId))->name : null;

            AssetTransfer::create([
                'asset_id'               => $asset->id,
                'from_user'              => $asset->assignee?->name,
                'to_user'                => $newAssignee?->name,
                'from_location'          => $asset->location?->name,
                'to_location'            => $newLocationName,
                'transferred_by_user_id' => auth()->id(),
                'notes'                  => $request->notes,
                'transferred_at'         => $request->transferred_at,
            ]);

            $asset->update([
                'assignee_type'                   => $request->to_assignee_type,
                'assignee_id'                     => $request->to_assignee_id,
                'location_id'                     => $newLocationId,
                'accountability_signed'           => Asset::ACCOUNTABILITY_PENDING,
                'accountability_uploaded_snipeit' => Asset::ACCOUNTABILITY_PENDING,
            ]);
        });

        return redirect()->route('assets.show', $asset)
            ->with('success', 'Asset transferred. Accountability status reset to pending.');
    }
}
