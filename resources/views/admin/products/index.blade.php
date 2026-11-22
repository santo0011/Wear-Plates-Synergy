@extends('layouts.admin')

@section('title', 'Products')
@section('actions')
    <a href="{{ route('admin.products.create', request()->only('category')) }}" class="btn btn-maroon"><i class="fa-solid fa-plus me-1"></i><span class="d-none d-md-inline">Add Product</span></a>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <form class="row g-2" method="GET">
            <div class="col-md-5"><input type="search" name="q" value="{{ request('q') }}" class="form-control" placeholder="Search products…"></div>
            <div class="col-md-4">
                <select name="category" class="form-select">
                    <option value="">All categories</option>
                    @foreach ($categories as $c)
                        <option value="{{ $c->id }}" @selected(request('category') == $c->id)>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button class="btn btn-outline-maroon flex-grow-1"><i class="fa-solid fa-filter me-1"></i>Filter</button>
                @if (request()->hasAny(['q', 'category']))<a href="{{ route('admin.products.index') }}" class="btn btn-light">Clear</a>@endif
            </div>
        </form>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead><tr><th>Image</th><th>Product</th><th>Category</th><th>Order</th><th>Status</th><th class="text-end">Actions</th></tr></thead>
            <tbody>
            @forelse ($products as $product)
                <tr>
                    <td>@if ($img = $product->display_image_url)<img src="{{ $img }}" alt="" class="thumb">@endif</td>
                    <td>
                        <strong>{{ $product->name }}</strong>
                        @if ($product->is_featured)<span class="badge text-bg-warning ms-1"><i class="fa-solid fa-star"></i> Featured</span>@endif
                        <div class="small text-muted">{{ \Illuminate\Support\Str::limit($product->short_description, 70) }}</div>
                    </td>
                    <td>{{ $product->category?->name }}</td>
                    <td>{{ $product->sort_order }}</td>
                    <td>{!! $product->is_active ? '<span class="badge text-bg-success">Active</span>' : '<span class="badge text-bg-secondary">Hidden</span>' !!}</td>
                    <td class="text-end text-nowrap">
                        @if ($product->category)
                            <a href="{{ route('products.show', [$product->category, $product]) }}" target="_blank" class="btn btn-sm btn-light" title="View on site"><i class="fa-solid fa-eye"></i></a>
                        @endif
                        <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-sm btn-outline-maroon" title="Edit"><i class="fa-solid fa-pen"></i></a>
                        <form action="{{ route('admin.products.destroy', $product) }}" method="POST" class="d-inline" data-confirm-title="Delete this product?" data-confirm="{{ $product->name }} and its image will be permanently deleted. This cannot be undone.">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger" title="Delete"><i class="fa-solid fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted py-4">No products found.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if ($products->hasPages())<div class="card-body">{{ $products->links() }}</div>@endif
</div>
@endsection
