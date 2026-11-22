@extends('layouts.admin')

@section('title', $location->exists ? 'Edit Location' : 'Add Location')
@section('actions')
    <a href="{{ route('admin.locations.index') }}" class="btn btn-light"><i class="fa-solid fa-arrow-left me-1"></i>Back</a>
@endsection

@section('content')
<form method="POST" action="{{ $location->exists ? route('admin.locations.update', $location) : route('admin.locations.store') }}">
    @csrf
    @if ($location->exists) @method('PUT') @endif
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header"><h2>Location Details</h2></div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6"><x-admin.input name="city" label="City" :value="$location->city" :required="true" maxlength="120" /></div>
                        <div class="col-md-6"><x-admin.input name="state" label="State" :value="$location->state" maxlength="120" /></div>
                    </div>
                    <div class="mb-3">
                        <label for="type" class="form-label">Type <span class="text-danger">*</span></label>
                        <select name="type" id="type" class="form-select @error('type') is-invalid @enderror" required>
                            @foreach (\App\Models\Location::TYPES as $key => $label)
                                <option value="{{ $key }}" @selected(old('type', $location->type) === $key)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <x-admin.textarea name="address" label="Verified Address" :value="$location->address" rows="3" maxlength="500" help="Optional. Only enter a confirmed, complete address." />
                    <x-admin.input name="phone" label="Phone" :value="$location->phone" maxlength="30" help="Optional." />
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header"><h2>Map &amp; Display</h2></div>
                <div class="card-body">
                    <x-admin.input name="latitude" type="number" step="0.000001" label="Latitude" :value="$location->latitude" help="City-level, e.g. 19.0760 for Mumbai." />
                    <x-admin.input name="longitude" type="number" step="0.000001" label="Longitude" :value="$location->longitude" help="e.g. 72.8777 for Mumbai. Leave both empty to hide from the map." />
                    <x-admin.input name="sort_order" type="number" label="Display Order" :value="$location->sort_order ?? 0" min="0" />
                    <x-admin.switch name="is_active" label="Visible on website" :checked="$location->is_active" />
                    <button class="btn btn-maroon w-100"><i class="fa-solid fa-floppy-disk me-2"></i>Save Location</button>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
