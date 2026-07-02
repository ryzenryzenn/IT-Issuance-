<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAssetRequest;
use App\Http\Requests\UpdateAssetRequest;
use App\Models\Asset;
use App\Models\AssetModel;
use App\Models\Category;
use App\Models\Company;
use App\Models\Employee;
use App\Models\Location;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class AssetController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Asset::class);
        $sort = $request->input('sort', 'created_at');
        $dir  = $request->input('dir', 'desc') === 'asc' ? 'asc' : 'desc';

        $allowedSorts = ['asset_tag', 'asset_model', 'location', 'date_issued', 'created_at'];
        if (! in_array($sort, $allowedSorts, true)) {
            $sort = 'created_at';
        }

        $query = Asset::query()
            ->with(['company', 'category', 'model', 'location', 'assignee'])
            ->search($request->input('q'))
            ->when($request->filled('company_id'), fn ($q) => $q->where('company_id', $request->company_id))
            ->when($request->filled('category_id'), fn ($q) => $q->where('category_id', $request->category_id))
            ->when($request->filled('location_id'), fn ($q) => $q->where('location_id', $request->location_id))
            ->when($request->filled('model_id'), fn ($q) => $q->where('model_id', $request->model_id))
            ->when($request->filled('accountability_signed'), fn ($q) => $q->where('accountability_signed', $request->accountability_signed))
            ->when($request->filled('accountability_uploaded_snipeit'), fn ($q) => $q->where('accountability_uploaded_snipeit', $request->accountability_uploaded_snipeit));

        // Sorting — the model/location keys live on related tables, so join to sort by name.
        if ($sort === 'asset_model') {
            $query->leftJoin('asset_models', 'assets.model_id', '=', 'asset_models.id')
                  ->orderBy('asset_models.name', $dir)->select('assets.*');
        } elseif ($sort === 'location') {
            $query->leftJoin('locations', 'assets.location_id', '=', 'locations.id')
                  ->orderBy('locations.name', $dir)->select('assets.*');
        } else {
            $query->orderBy('assets.'.$sort, $dir);
        }

        $assets     = $query->paginate(15)->withQueryString();
        $companies  = Company::orderBy('name')->get();
        $categories = Category::orderBy('name')->get();
        $models     = AssetModel::orderBy('name')->get();
        $locations  = Location::orderBy('name')->get();
        $employees  = Employee::where('is_active', true)->orderBy('name')->get();
        $assetOptions = Asset::with('assignee')->orderBy('asset_tag')->get()
            ->map(fn ($a) => [
                'id'        => $a->id,
                'asset_tag' => $a->asset_tag,
                'serial'    => $a->serial_number,
                'assignee'  => $a->assignee?->name,
            ])->values();

        return view('assets.index', compact('assets', 'companies', 'categories', 'models', 'locations', 'employees', 'assetOptions', 'sort', 'dir'));
    }

    public function create()
    {
        $this->authorize('create', Asset::class);
        return view('assets.create', [
            'companies'  => Company::orderBy('name')->get(),
            'categories' => Category::orderBy('name')->get(),
            'models'     => AssetModel::orderBy('name')->get(),
            'locations'  => Location::orderBy('name')->get(),
            'employees'  => Employee::where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function store(StoreAssetRequest $request)
    {
        $asset = Asset::create($request->validated());

        if ($request->expectsJson()) {
            $asset->load(['company', 'category', 'model', 'location', 'assignee']);

            return response()->json([
                'id'      => $asset->id,
                'message' => 'Asset created successfully.',
                'html'    => view('assets._row', ['a' => $asset])->render(),
            ], 201);
        }

        return redirect()->route('assets.show', $asset)
            ->with('success', 'Asset created successfully.');
    }

    public function show(Asset $asset)
    {
        $this->authorize('view', $asset);
        $asset->load(['company', 'category', 'model', 'location', 'assignee', 'transfers.transferredBy', 'accountabilityFiles.uploadedBy']);

        $activities = \Spatie\Activitylog\Models\Activity::where('subject_type', Asset::class)
            ->where('subject_id', $asset->id)
            ->with('causer')
            ->latest()
            ->limit(50)
            ->get();

        $qrSvg = QrCode::format('svg')->size(180)->margin(1)->generate($asset->qrPayload());

        $locations = Location::orderBy('name')->get();
        $employees = Employee::where('is_active', true)->orderBy('name')->get();

        return view('assets.show', compact('asset', 'activities', 'qrSvg', 'locations', 'employees'));
    }

    public function label(Asset $asset)
    {
        $this->authorize('view', $asset);
        $asset->load(['company', 'category']);

        $qrSvg = QrCode::format('svg')->size(220)->margin(1)->generate($asset->qrPayload());

        return view('assets.label', compact('asset', 'qrSvg'));
    }

    public function transmittal(Request $request)
    {
        $assets = $this->assetsFromIds($request);

        return view('assets.transmittal', compact('assets'));
    }

    public function gatePass(Request $request)
    {
        $assets = $this->assetsFromIds($request);

        return view('assets.gate-pass', compact('assets'));
    }

    /**
     * Resolve a comma-separated `ids` query string into a collection of assets
     * (shared by the transmittal and gate-pass forms).
     */
    private function assetsFromIds(Request $request)
    {
        abort_unless($request->user()->can('view assets'), 403);

        $ids = collect(explode(',', (string) $request->query('ids')))
            ->filter(fn ($v) => is_numeric($v))
            ->map(fn ($v) => (int) $v)
            ->unique()
            ->values();

        abort_if($ids->isEmpty(), 404, 'No assets selected.');

        $assets = Asset::with(['model', 'location', 'category'])
            ->whereIn('id', $ids)
            ->orderBy('asset_tag')
            ->get();

        abort_if($assets->isEmpty(), 404, 'No assets found.');

        return $assets;
    }

    public function edit(Asset $asset)
    {
        $this->authorize('update', $asset);
        return view('assets.edit', [
            'asset'      => $asset,
            'companies'  => Company::orderBy('name')->get(),
            'categories' => Category::orderBy('name')->get(),
            'models'     => AssetModel::orderBy('name')->get(),
            'locations'  => Location::orderBy('name')->get(),
            'employees'  => Employee::where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function update(UpdateAssetRequest $request, Asset $asset)
    {
        $asset->update($request->validated());

        return redirect()->route('assets.show', $asset)
            ->with('success', 'Asset updated successfully.');
    }

    public function destroy(Asset $asset)
    {
        $this->authorize('delete', $asset);
        $asset->delete();

        return redirect()->route('assets.index')
            ->with('success', 'Asset deleted (soft).');
    }

    public function bulkDestroy(Request $request)
    {
        abort_unless($request->user()->can('delete assets'), 403);

        $validated = $request->validate([
            'ids'   => ['required', 'array'],
            'ids.*' => ['integer', 'exists:assets,id'],
        ]);

        $count = Asset::whereIn('id', $validated['ids'])->delete();

        return redirect()->route('assets.index')
            ->with('success', "{$count} asset(s) deleted (soft).");
    }
}
