<?php

namespace App\Http\Controllers;

use App\Exports\AssetsExport;
use App\Models\Asset;
use App\Models\Category;
use App\Models\Company;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        if (! $request->user()->can('export reports')) {
            abort(403);
        }

        return view('reports.index', [
            'companies'  => Company::orderBy('name')->get(),
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function assetsPdf(Request $request)
    {
        $this->authorizeOrAbort($request);
        $assets = $this->filteredAssets($request)->get();

        $pdf = Pdf::loadView('reports.pdf.assets', [
            'assets'      => $assets,
            'generatedAt' => now(),
            'filters'     => $request->only(['company_id', 'category_id', 'accountability_signed', 'accountability_uploaded_snipeit', 'from', 'to']),
        ])->setPaper('a4', 'landscape');

        return $pdf->download('assets-report-'.now()->format('Ymd-His').'.pdf');
    }

    public function assetsExcel(Request $request)
    {
        $this->authorizeOrAbort($request);

        return Excel::download(
            new AssetsExport($this->filteredAssets($request)),
            'assets-report-'.now()->format('Ymd-His').'.xlsx'
        );
    }

    public function assetsCsv(Request $request)
    {
        $this->authorizeOrAbort($request);

        return Excel::download(
            new AssetsExport($this->filteredAssets($request)),
            'assets-report-'.now()->format('Ymd-His').'.csv',
            \Maatwebsite\Excel\Excel::CSV
        );
    }

    private function authorizeOrAbort(Request $request): void
    {
        if (! $request->user()->can('export reports')) {
            abort(403);
        }
    }

    private function filteredAssets(Request $request)
    {
        return Asset::query()
            ->with(['company', 'category', 'model', 'location', 'assignee'])
            ->when($request->filled('company_id'),  fn ($q) => $q->where('company_id', $request->company_id))
            ->when($request->filled('category_id'), fn ($q) => $q->where('category_id', $request->category_id))
            ->when($request->filled('accountability_signed'), fn ($q) => $q->where('accountability_signed', $request->accountability_signed))
            ->when($request->filled('accountability_uploaded_snipeit'), fn ($q) => $q->where('accountability_uploaded_snipeit', $request->accountability_uploaded_snipeit))
            ->when($request->filled('from'), fn ($q) => $q->whereDate('date_issued', '>=', $request->from))
            ->when($request->filled('to'),   fn ($q) => $q->whereDate('date_issued', '<=', $request->to))
            ->orderBy('asset_tag');
    }
}
