@extends('layouts.app')

@section('title', $category->meta_title ?: $category->name)
@section('meta_description', (string) ($category->meta_description ?: $category->short_description))
@section('canonical', route('products.category', $category))

@php $photo = $category->image && str_starts_with($category->image, 'uploads/') ? $category->image : null; @endphp

@section('content')
<x-page-hero :title="$category->name" :subtitle="$category->short_description" :image="$photo" :crumbs="['Products' => route('products.index')]" />

<section class="section">
    <div class="container-wide">
        <div class="row g-5">
            <div class="col-lg-8">
                <div class="prose lead-first">{{ rich_text($category->description) }}</div>

                <h2 class="sub-title mt-5 mb-4">Products in this category <span class="count-pill">{{ $products->count() }}</span></h2>
                <div class="row g-4">
                    @forelse ($products as $product)
                        @php $product->setRelation('category', $category); @endphp
                        <div class="col-md-6 reveal" data-delay="{{ $loop->index % 2 }}">
                            @include('products.partials.product-card', ['product' => $product])
                        </div>
                    @empty
                        <p class="text-muted">Products for this category will be listed soon. Please contact us for details.</p>
                    @endforelse
                </div>
            </div>
            <aside class="col-lg-4">
                <div class="sticky-aside">
                    @include('products.partials.enquiry-sidebar', ['subject' => $category->name])
                    @if ($otherCategories->isNotEmpty())
                        <div class="side-card mt-4">
                            <h3 class="side-title">Other Categories</h3>
                            <ul class="side-links">
                                @foreach ($otherCategories as $other)
                                    <li><a href="{{ route('products.category', $other) }}"><i class="{{ $other->icon_class }}"></i>{{ $other->name }}</a></li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            </aside>
        </div>
    </div>
</section>
@endsection
