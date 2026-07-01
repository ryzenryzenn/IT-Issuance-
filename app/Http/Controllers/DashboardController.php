<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Category;
use App\Models\Company;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $totalAssets         = Asset::count();
        $assignedAssets      = Asset::assigned()->count();
        $pendingSignature    = Asset::pendingSignature()->count();
        $pendingSnipeit      = Asset::pendingSnipeIt()->count();
        $totalCompanies      = Company::count();
        $totalCategories     = Category::count();
        $signedAssets        = Asset::where('accountability_signed', Asset::ACCOUNTABILITY_YES)->count();

        $recentAssets   = Asset::with(['company', 'category', 'model', 'assignee'])->latest()->limit(8)->get();
        $recentActivity = Activity::with('causer')->latest()->limit(10)->get();

        $perCompany = Company::withCount('assets')->orderByDesc('assets_count')->get();
        $perCategory = Category::withCount('assets')->orderByDesc('assets_count')->get();

        // Days in the current month that have assets issued (for the calendar).
        $monthStart = now()->startOfMonth();
        $issuedDays = Asset::whereNotNull('date_issued')
            ->whereYear('date_issued', $monthStart->year)
            ->whereMonth('date_issued', $monthStart->month)
            ->get()
            ->groupBy(fn ($a) => (int) $a->date_issued->day)
            ->map->count()
            ->all();

        // Assets registered per month over the last 6 months.
        $assetsPerMonth = collect(range(5, 0))->map(function ($i) {
            $month = now()->subMonths($i);

            return [
                'label' => $month->format('M Y'),
                'count' => Asset::whereYear('created_at', $month->year)
                    ->whereMonth('created_at', $month->month)
                    ->count(),
            ];
        });

        return view('dashboard', compact(
            'totalAssets', 'assignedAssets', 'pendingSignature', 'pendingSnipeit',
            'totalCompanies', 'totalCategories', 'signedAssets', 'recentAssets', 'recentActivity',
            'perCompany', 'perCategory', 'assetsPerMonth', 'issuedDays'
        ));
    }
}
