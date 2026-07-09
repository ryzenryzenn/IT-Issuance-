<?php

namespace App\Http\Controllers;

use App\Actions\Assets\TransferAsset;
use App\Http\Requests\StoreAssetTransferRequest;
use App\Models\Asset;

class AssetTransferController extends Controller
{
    public function store(StoreAssetTransferRequest $request, Asset $asset, TransferAsset $transferAsset)
    {
        $this->authorize('transfer', $asset);

        $transferAsset->handle($asset, $request->validated(), (int) $request->user()->id);

        return redirect()->route('assets.show', $asset)
            ->with('success', 'Asset transferred. Accountability status reset to pending.');
    }
}
