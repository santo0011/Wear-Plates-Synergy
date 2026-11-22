@extends('layouts.admin')

@section('title', 'Product Categories')
@section('actions')
    <a href="{{ route('admin.categories.create') }}" class="btn btn-maroon"><i class="fa-solid fa-plus me-1"></i><span class="d-none d-md-inline">Add Category</span></a>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <form class="d-flex gap-2" method="GET">
            <input type="search" name="q" value="{{ request('q') }}" class="form-control" placeholder="Search categories…" style="max-width: 320px">
            <button class="btn btn-outline-maroon"><i class="fa-solid fa-magnifying-glass"></i></button>
            @if (request('q'))<a href="{{ route('admin.categories.index') }}" class="btn btn-light">Clear</a>@endif
        </form>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead><tr><th>Image</th><th>Name</th><th>Products</th><th>Order</th><th>Status</th><th class="text-end">Actions</th></tr></thead>
            <tbody>
            @forelse ($categories as $category)
                <tr>
                    <td>@if ($category->image_url)<img src="{{ $category->image_url }}" alt="" class="thumb">@else<span class="thumb d-inline-grid place-items-center"></span>@endif</td>
                    <td>
                        <i class="{{ $category->icon_class }} text-maroon me-1"></i><strong>{{ $category->name }}</strong>
                        <div class="small text-muted">/products/{{ $category->slug }}</div>
                    </td>
                    <td><a href="{{ route('admin.products.index', ['category' => $category->id]) }}">{{ $category->products_count }}</a></td>
                    <td>{{ $category->sort_order }}</td>
                    <td>{!! $category->is_active ? '<span class="badge text-bg-success">Active</span>' : '<span class="badge text-bg-secondary">Hidden</span>' !!}</td>
                    <td class="text-end text-nowrap">
                        <a href="{{ route('products.category', $category) }}" target="_blank" class="btn btn-sm btn-light" title="View on site"><i class="fa-solid fa-eye"></i></a>
                        <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-sm btn-outline-maroon" title="Edit"><i class="fa-solid fa-pen"></i></a>
                        <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="d-inline" data-confirm-title="Delete this category?" data-confirm="{{ $category->name }} will be permanently deleted. This cannot be undone.">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger" title="Delete"><i class="fa-solid fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted py-4">No categories found.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if ($categories->hasPages())<div class="card-body">{{ $categories->links() }}</div>@endif
</div>
@endsection
