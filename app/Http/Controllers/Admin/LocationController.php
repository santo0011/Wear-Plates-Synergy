<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Location;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class LocationController extends Controller
{
    public function index(): View
    {
        return view('admin.locations.index', ['locations' => Location::ordered()->get()]);
    }

    public function create(): View
    {
        return view('admin.locations.form', ['location' => new Location(['type' => 'regional', 'is_active' => true])]);
    }

    public function store(Request $request): RedirectResponse
    {
        Location::create($this->validated($request));

        return redirect()->route('admin.locations.index')->with('success', 'Location added.');
    }

    public function edit(Location $location): View
    {
        return view('admin.locations.form', compact('location'));
    }

    public function update(Request $request, Location $location): RedirectResponse
    {
        $location->update($this->validated($request));

        return redirect()->route('admin.locations.index')->with('success', 'Location updated.');
    }

    public function destroy(Location $location): RedirectResponse
    {
        $location->delete();

        return redirect()->route('admin.locations.index')->with('success', 'Location deleted.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'city' => ['required', 'string', 'max:120'],
            'state' => ['nullable', 'string', 'max:120'],
            'type' => ['required', Rule::in(array_keys(Location::TYPES))],
            'address' => ['nullable', 'string', 'max:500'],
            'phone' => ['nullable', 'string', 'max:30'],
            // Limited to the area covered by the India map.
            'latitude' => ['nullable', 'numeric', 'between:6,37.5', 'required_with:longitude'],
            'longitude' => ['nullable', 'numeric', 'between:68,98', 'required_with:latitude'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:65535'],
        ]);

        $data['is_active'] = $request->boolean('is_active');
        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);

        return $data;
    }
}
