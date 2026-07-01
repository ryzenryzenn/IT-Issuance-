<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        if (! $request->user()->can('view audit logs')) {
            abort(403);
        }

        $activities = Activity::query()
            ->with('causer')
            ->when($request->filled('log_name'), fn ($q) => $q->where('log_name', $request->log_name))
            ->when($request->filled('event'),    fn ($q) => $q->where('event', $request->event))
            ->when($request->filled('causer_id'),fn ($q) => $q->where('causer_id', $request->causer_id))
            ->latest()
            ->paginate(25)
            ->withQueryString();

        $logNames = Activity::query()->select('log_name')->distinct()->pluck('log_name')->filter()->values();
        $events   = Activity::query()->select('event')->distinct()->pluck('event')->filter()->values();

        return view('audit-logs.index', compact('activities', 'logNames', 'events'));
    }

    public function show(Request $request, Activity $activity)
    {
        if (! $request->user()->can('view audit logs')) {
            abort(403);
        }

        $activity->load('causer', 'subject');

        return view('audit-logs.show', compact('activity'));
    }
}
