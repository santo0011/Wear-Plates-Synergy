@extends('layouts.admin')

@section('title', 'Locations / Presence')
@section('actions')
    <a href="{{ route('admin.locations.create') }}" class="btn btn-maroon"><i class="fa-solid fa-plus me-1"></i><span class="d-none d-md-inline">Add Location</span></a>
@endsection

@section('content')
<div class="alert alert-info small"><i class="fa-solid fa-circle-info me-1"></i>These locations appear on the "Our Presence" map and contact page. Only enter verified addresses; latitude/longitude are city-level and used to place map pins.</div>
<div class="card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead><tr><th>City</th><th>Type</th><th>State</th><th>Map Pin</th><th>Status</th><th class="text-end">Actions</th></tr></thead>
            <tbody>
            @forelse ($locations as $loc)
                <tr>
                    <td><strong>{{ $loc->city }}</strong>@if ($loc->address)<div class="small text-muted">{{ \Illuminate\Support\Str::limit($loc->address, 60) }}</div>@endif</td>
                    <td><span class="badge {{ $loc->type === 'regional' ? 'text-bg-light border' : ($loc->type === 'plant' ? 'text-bg-warning' : 'text-bg-dark') }}">{{ $loc->type_label }}</span></td>
                    <td>{{ $loc->state }}</td>
                    <td>{!! $loc->has_map_point ? '<i class="fa-solid fa-location-dot text-maroon"></i> '.e($loc->latitude.', '.$loc->longitude) : '<span class="text-muted small">Not on map</span>' !!}</td>
                    <td>{!! $loc->is_active ? '<span class="badge text-bg-success">Active</span>' : '<span class="badge text-bg-secondary">Hidden</span>' !!}</td>
                    <td class="text-end text-nowrap">
                        <a href="{{ route('admin.locations.edit', $loc) }}" class="btn btn-sm btn-outline-maroon"><i class="fa-solid fa-pen"></i></a>
                        <form action="{{ route('admin.locations.destroy', $loc) }}" method="POST" class="d-inline" data-confirm-title="Delete this location?" data-confirm="{{ $loc->city }} will be removed from the website and the India map.">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted py-4">No locations yet.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
