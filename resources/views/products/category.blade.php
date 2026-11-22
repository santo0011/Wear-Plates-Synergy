@extends('layouts.app')

@section('title', $category->meta_title ?: $category->name)
@section('meta_description', (string) ($category->meta_description ?: ($category->short_description ?: setting('seo_products_description'))))

@section('content')
<x-page-hero :title="$category->name" :subtitle="$category->short_description"
    :breadcrumbs="['Products & Solutions' => route('products.index'), $category->name => null]" />

<section class="section">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-8">
                <div class="row g-4 align-items-center mb-5">
                    @if ($category->image_url)
                        <div class="col-md-5 reveal reveal-left">
                            <div class="product-gallery"><img src="{{ $category->image_url }}" alt="{{ $category->name }}" width="800" height="560"></div>
                        </div>
                    @endif
                    <div class="col reveal reveal-right">
                        <span class="eyebrow">Category</span>
                        <h2 class="section-title fs-2">{{ $category->name }}</h2>
                        @if ($category->description)
                            <div class="product-desc">{!! nl2br(e($category->description)) !!}</div>
                        @endif
                    </div>
                </div>

                <div class="row g-4">
                    @forelse ($products as $i => $product)
                        @php $product->setRelation('category', $category); @endphp
                        <div class="col-md-6 reveal" data-delay="{{ ($i % 2) + 1 }}">
                            <x-product-card :product="$product" />
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="info-panel text-center">
                                <i class="fa-solid fa-box-open fs-2 text-maroon mb-2"></i>
                                <p class="mb-3">Products in this category are available on request.</p>
                                <a href="{{ route('contact') }}#enquiry" class="btn btn-maroon">Send an Enquiry</a>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>

            <aside class="col-lg-4">
                <div class="position-sticky" style="top: 110px">
                    <div class="info-panel mb-4">
                        <h3 class="fs-5 text-uppercase mb-3">Product Categories</h3>
                        <div class="list-group side-nav">
                            <a href="{{ route('products.category', $category) }}" class="list-group-item list-group-item-action active"><i class="{{ $category->icon_class }}"></i>{{ $category->name }}</a>
                            @foreach ($otherCategories as $other)
                                <a href="{{ route('products.category', $other) }}" class="list-group-item list-group-item-action"><i class="{{ $other->icon_class }}"></i>{{ $other->name }}</a>
                            @endforeach
                        </div>
                    </div>
                    @include('products.partials.enquiry-sidebar', ['subject' => $category->name])
                </div>
            </aside>
        </div>
    </div>
</section>
@endsection
