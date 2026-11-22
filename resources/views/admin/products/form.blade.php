@extends('layouts.admin')

@section('title', $product->exists ? 'Edit Product' : 'Add Product')
@section('actions')
    <a href="{{ route('admin.products.index') }}" class="btn btn-light"><i class="fa-solid fa-arrow-left me-1"></i>Back</a>
@endsection

@section('content')
@if ($categories->isEmpty())
    <div class="alert alert-warning">Please <a href="{{ route('admin.categories.create') }}">create a product category</a> first.</div>
@endif
<form method="POST" enctype="multipart/form-data"
      action="{{ $product->exists ? route('admin.products.update', $product) : route('admin.products.store') }}">
    @csrf
    @if ($product->exists) @method('PUT') @endif
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header"><h2>Product Details</h2></div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="product_category_id" class="form-label">Category <span class="text-danger">*</span></label>
                        <select name="product_category_id" id="product_category_id" class="form-select @error('product_category_id') is-invalid @enderror" required>
                            <option value="">Select a category…</option>
                            @foreach ($categories as $c)
                                <option value="{{ $c->id }}" @selected(old('product_category_id', $product->product_category_id) == $c->id)>{{ $c->name }}</option>
                            @endforeach
                        </select>
                        @error('product_category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <x-admin.input name="name" label="Product Name" :value="$product->name" :required="true" maxlength="255" data-slug-source />
                    <x-admin.input name="subtitle" label="Subtitle / Grade Line" :value="$product->subtitle" maxlength="255" help="Short line under the name, e.g. C-Cr alloy · 55–60 HRC." />
                    <x-admin.input name="slug" label="URL Slug" :value="$product->slug" maxlength="255" data-slug-target help="Leave empty to generate from the name." />
                    <x-admin.textarea name="short_description" label="Short Description" :value="$product->short_description" rows="2" maxlength="500" help="Shown on product cards." />
                    <x-admin.textarea name="description" label="Full Description" :value="$product->description" rows="9" help="Shown on the product page. Blank line = new paragraph. **bold** supported." />
                </div>
            </div>
            <div class="card mt-4">
                <div class="card-header"><h2>Technical Details</h2></div>
                <div class="card-body">
                    <x-admin.textarea name="highlights" label="Key Features" :value="$product->highlights" rows="5" help="One feature per line. Shown as a checklist." />
                    <x-admin.textarea name="specifications" label="Specifications" :value="$product->specifications" rows="8" class="mono" help="One per line in the form: Label | Value   (e.g. Hardness | 55–60 HRC)" />
                    <x-admin.input name="data_table_title" label="Data Table Title" :value="$product->data_table_title" maxlength="255" help="e.g. Chemical Composition & Properties" />
                    <x-admin.textarea name="data_table" label="Data Table" :value="$product->data_table" rows="7" class="mono" help="One row per line, columns separated by |. The first line is the header row." />
                    <x-admin.textarea name="applications" label="Applications" :value="$product->applications" rows="5" help="One application per line." />
                </div>
            </div>
            <div class="card mt-4">
                <div class="card-header"><h2>SEO</h2></div>
                <div class="card-body">
                    <x-admin.input name="meta_title" label="Meta Title" :value="$product->meta_title" maxlength="255" help="Defaults to the product name." />
                    <x-admin.textarea name="meta_description" label="Meta Description" :value="$product->meta_description" rows="2" maxlength="500" help="Defaults to the short description." />
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header"><h2>Publish</h2></div>
                <div class="card-body">
                    <x-admin.switch name="is_active" label="Visible on website" :checked="$product->is_active" />
                    <x-admin.switch name="is_featured" label="Featured on home page" :checked="$product->is_featured" />
                    <x-admin.input name="sort_order" type="number" label="Display Order" :value="$product->sort_order ?? 0" min="0" help="Lower numbers appear first." />
                    <x-admin.image name="image" label="Product Image" :current="$product->image" help="JPG, PNG or WebP. Max 6 MB. If empty, the category image is shown." />
                    <button class="btn btn-maroon w-100"><i class="fa-solid fa-floppy-disk me-2"></i>Save Product</button>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
