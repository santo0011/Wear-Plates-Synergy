@extends('layouts.app')

@section('title', 'Products & Solutions')
@section('meta_description', (string) (setting('seo_products_description')))

@section('content')
<x-page-hero title="Products & Solutions" subtitle="Wear protection products and solutions for industry – enquire for sizes, grades and availability." :breadcrumbs="['Products & Solutions' => null]" />

<section class="section">
    <div class="container">
        <x-section-heading eyebrow="Our Range" title='Product <span class="hl">Categories</span>' subtitle="Select a category to view its products and send an enquiry." />
        <div class="row g-4">
            @forelse ($categories as $i => $category)
                <div class="col-md-6 col-lg-4 reveal" data-delay="{{ ($i % 3) + 1 }}">
                    <x-category-card :category="$category" :index="$i + 1" :show-products="true" />
                </div>
            @empty
                <div class="col-12 text-center text-muted">Product information will be available soon.</div>
            @endforelse
        </div>
    </div>
</section>

<section class="section bg-light-gray">
    <div class="container">
        <x-section-heading eyebrow="All Products" title='Browse <span class="hl">Products</span>' />
        @foreach ($categories as $category)
            @continue($category->activeProducts->isEmpty())
            <div class="mb-5">
                <div class="d-flex align-items-center gap-3 mb-4 reveal">
                    <span class="loc-card-icon"><i class="{{ $category->icon_class }}"></i></span>
                    <h3 class="m-0 text-uppercase fs-3">{{ $category->name }}</h3>
                    <span class="flex-grow-1 border-top d-none d-md-block"></span>
                    <a href="{{ route('products.category', $category) }}" class="btn btn-sm btn-outline-maroon">View Category</a>
                </div>
                <div class="row g-4">
                    @foreach ($category->activeProducts as $i => $product)
                        @php $product->setRelation('category', $category); @endphp
                        <div class="col-md-6 col-lg-4 reveal" data-delay="{{ ($i % 3) + 1 }}">
                            <x-product-card :product="$product" />
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</section>

<section class="section">
    <div class="container"><x-cta-band title="Can't Find What You Need?" text="Tell us about your application – we offer custom fabrications and engineering support." /></div>
</section>
@endsection
