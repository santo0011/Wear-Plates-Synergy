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
                    <x-admin.input name="slug" label="URL Slug" :value="$product->slug" maxlength="255" data-slug-target help="Leave empty to generate from the name." />
                    <x-admin.textarea name="short_description" label="Short Description" :value="$product->short_description" rows="2" maxlength="500" help="Shown on product cards." />
                    <x-admin.textarea name="description" label="Full Description" :value="$product->description" rows="9" help="Shown on the product page. Only enter verified technical information." />
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
                    <x-admin.image name="image" label="Product Image" :current="$product->image" help="JPG, PNG or WebP. Max 3 MB. If empty, the category image is shown." />
                    <button class="btn btn-maroon w-100"><i class="fa-solid fa-floppy-disk me-2"></i>Save Product</button>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
