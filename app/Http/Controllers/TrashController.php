<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetModel;
use App\Models\Category;
use App\Models\Company;
use App\Models\Employee;
use App\Models\Location;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class TrashController extends Controller
{
    /** Soft-deletable resources that can be restored from the trash. */
    private function types(): array
    {
        return [
            'assets'       => ['model' => Asset::class,      'label' => 'Assets'],
            'employees'    => ['model' => Employee::class,   'label' => 'Employees'],
            'companies'    => ['model' => Company::class,    'label' => 'Companies'],
            'categories'   => ['model' => Category::class,   'label' => 'Categories'],
            'locations'    => ['model' => Location::class,   'label' => 'Locations'],
            'asset-models' => ['model' => AssetModel::class, 'label' => 'Asset Models'],
            'users'        => ['model' => User::class,       'label' => 'Users'],
        ];
    }

    private function config(string $type): array
    {
        return $this->types()[$type] ?? abort(404, 'Unknown trash type.');
    }

    public function index(Request $request)
    {
        abort_unless($request->user()->can('view trash'), 403);

        $types  = $this->types();
        $counts = [];
        foreach ($types as $slug => $cfg) {
            $counts[$slug] = $cfg['model']::onlyTrashed()->count();
        }

        $active = $request->input('type', 'assets');
        if (! isset($types[$active])) {
            $active = 'assets';
        }

        $query = $types[$active]['model']::onlyTrashed()->orderByDesc('deleted_at');
        if ($active === 'assets') {
            $query->with(['model', 'company']);
        }

        $items = $query->paginate(15)->withQueryString();

        return view('trash.index', compact('types', 'counts', 'active', 'items'));
    }

    public function restore(Request $request, string $type, int $id)
    {
        abort_unless($request->user()->can('restore records'), 403);

        $cfg    = $this->config($type);
        $record = $cfg['model']::onlyTrashed()->findOrFail($id);
        $record->restore();

        return back()->with('success', $cfg['label'].' record restored.');
    }

    public function forceDelete(Request $request, string $type, int $id)
    {
        abort_unless($request->user()->can('force delete records'), 403);

        $cfg    = $this->config($type);
        $record = $cfg['model']::onlyTrashed()->findOrFail($id);

        try {
            $record->forceDelete();
        } catch (QueryException $e) {
            return back()->with('error', 'Cannot permanently delete — this record is still referenced by other data.');
        }

        return back()->with('success', $cfg['label'].' record permanently deleted.');
    }
}
