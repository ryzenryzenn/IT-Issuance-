<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAssetModelRequest;
use App\Http\Requests\UpdateAssetModelRequest;
use App\Models\AssetModel;
use Illuminate\Http\Request;

class AssetModelController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', AssetModel::class);

        $models = AssetModel::query()
            ->withCount('assets')
            ->when($request->filled('q'), function ($q) use ($request) {
                $like = '%'.$request->q.'%';
                $q->where(fn ($w) => $w->where('name', 'like', $like)->orWhere('description', 'like', $like));
            })
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('asset-models.index', compact('models'));
    }

    public function create()
    {
        $this->authorize('create', AssetModel::class);
        return view('asset-models.create');
    }

    public function store(StoreAssetModelRequest $request)
    {
        AssetModel::create($request->validated());

        return redirect()->route('asset-models.index')->with('success', 'Asset model created.');
    }

    public function edit(AssetModel $assetModel)
    {
        $this->authorize('update', $assetModel);
        return view('asset-models.edit', compact('assetModel'));
    }

    public function update(UpdateAssetModelRequest $request, AssetModel $assetModel)
    {
        $assetModel->update($request->validated());

        return redirect()->route('asset-models.index')->with('success', 'Asset model updated.');
    }

    public function destroy(AssetModel $assetModel)
    {
        $this->authorize('delete', $assetModel);

        if ($assetModel->assets()->exists()) {
            return back()->with('error', 'Cannot delete a model that still has assets.');
        }

        $assetModel->delete();

        return redirect()->route('asset-models.index')->with('success', 'Asset model deleted.');
    }
}
