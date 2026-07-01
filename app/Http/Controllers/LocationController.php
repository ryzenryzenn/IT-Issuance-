<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLocationRequest;
use App\Http\Requests\UpdateLocationRequest;
use App\Models\Location;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Location::class);

        $locations = Location::query()
            ->withCount('assets')
            ->when($request->filled('q'), function ($q) use ($request) {
                $like = '%'.$request->q.'%';
                $q->where(fn ($w) => $w->where('name', 'like', $like)->orWhere('description', 'like', $like));
            })
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('locations.index', compact('locations'));
    }

    public function create()
    {
        $this->authorize('create', Location::class);
        return view('locations.create');
    }

    public function store(StoreLocationRequest $request)
    {
        Location::create($request->validated());

        return redirect()->route('locations.index')->with('success', 'Location created.');
    }

    public function edit(Location $location)
    {
        $this->authorize('update', $location);
        return view('locations.edit', compact('location'));
    }

    public function update(UpdateLocationRequest $request, Location $location)
    {
        $location->update($request->validated());

        return redirect()->route('locations.index')->with('success', 'Location updated.');
    }

    public function destroy(Location $location)
    {
        $this->authorize('delete', $location);

        if ($location->assets()->exists()) {
            return back()->with('error', 'Cannot delete a location that still has assets.');
        }

        $location->delete();

        return redirect()->route('locations.index')->with('success', 'Location deleted.');
    }
}
