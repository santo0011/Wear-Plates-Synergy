@extends('layouts.admin')

@section('title', $category->exists ? 'Edit Category' : 'Add Category')
@section('actions')
    <a href="{{ route('admin.categories.index') }}" class="btn btn-light"><i class="fa-solid fa-arrow-left me-1"></i>Back</a>
@endsection

@section('content')
<form method="POST" enctype="multipart/form-data"
      action="{{ $category->exists ? route('admin.categories.update', $category) : route('admin.categories.store') }}">
    @csrf
    @if ($category->exists) @method('PUT') @endif
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header"><h2>Category Details</h2></div>
                <div class="card-body">
                    <x-admin.input name="name" label="Name" :value="$category->name" :required="true" maxlength="255" data-slug-source />
                    <x-admin.input name="slug" label="URL Slug" :value="$category->slug" maxlength="255" data-slug-target help="Used in the page URL. Leave empty to generate from the name." />
                    <x-admin.textarea name="short_description" label="Short Description" :value="$category->short_description" rows="2" maxlength="500" help="Shown on category cards (max 500 characters)." />
                    <x-admin.textarea name="description" label="Full Description" :value="$category->description" rows="7" help="Shown on the category page. Line breaks are kept." />
                </div>
            </div>
            <div class="card mt-4">
                <div class="card-header"><h2>SEO</h2></div>
                <div class="card-body">
                    <x-admin.input name="meta_title" label="Meta Title" :value="$category->meta_title" maxlength="255" help="Defaults to the category name." />
                    <x-admin.textarea name="meta_description" label="Meta Description" :value="$category->meta_description" rows="2" maxlength="500" help="Defaults to the short description." />
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header"><h2>Publish</h2></div>
                <div class="card-body">
                    <x-admin.switch name="is_active" label="Visible on website" :checked="$category->is_active" />
                    <x-admin.input name="sort_order" type="number" label="Display Order" :value="$category->sort_order ?? 0" min="0" help="Lower numbers appear first." />
                    <x-admin.input name="icon" label="Icon (Font Awesome class)" :value="$category->icon" maxlength="80" help='e.g. "fa-solid fa-layer-group" – see fontawesome.com/icons' />
                    <x-admin.image name="image" label="Category Image" :current="$category->image" />
                    <button class="btn btn-maroon w-100"><i class="fa-solid fa-floppy-disk me-2"></i>Save Category</button>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
