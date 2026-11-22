@extends('layouts.app')

@section('title', $product->meta_title ?: $product->name)
@section('meta_description', (string) ($product->meta_description ?: ($product->short_description ?: $category->short_description)))

@section('content')
<x-page-hero :title="$product->name" :subtitle="$product->short_description"
    :breadcrumbs="['Products & Solutions' => route('products.index'), $category->name => route('products.category', $category), $product->name => null]" />

<section class="section">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-8">
                <div class="row g-4">
                    <div class="col-md-6 reveal reveal-left">
                        <div class="product-gallery">
                            @if ($img = $product->display_image_url)
                                <img src="{{ $img }}" alt="{{ $product->name }}" width="800" height="600">
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6 reveal reveal-right">
                        <a href="{{ route('products.category', $category) }}" class="product-meta-chip mb-3"><i class="{{ $category->icon_class }}"></i>{{ $category->name }}</a>
                        <h2 class="section-title fs-2">{{ $product->name }}</h2>
                        @if ($product->short_description)
                            <p class="lead fs-6 text-muted">{{ $product->short_description }}</p>
                        @endif
                        <div class="d-flex flex-wrap gap-2 mt-4">
                            <a href="{{ route('contact', ['product' => $product->slug]) }}#enquiry" class="btn btn-maroon"><i class="fa-solid fa-paper-plane me-2"></i>Enquire Now</a>
                            @if ($wa = whatsapp_url('Hello, I would like to enquire about: '.$product->name.' ('.$category->name.')'))
                                <a href="{{ $wa }}" target="_blank" rel="noopener" class="btn btn-whatsapp"><i class="fa-brands fa-whatsapp me-2"></i>WhatsApp</a>
                            @endif
                        </div>
                    </div>
                </div>

                @if ($product->description)
                    <div class="mt-5 reveal">
                        <h3 class="text-uppercase fs-4 mb-3">Product Overview</h3>
                        <div class="divider-gold mb-4"></div>
                        <div class="product-desc">{!! nl2br(e($product->description)) !!}</div>
                    </div>
                @endif

                <div class="info-panel mt-5 reveal">
                    <div class="row g-3 text-center">
                        <div class="col-6 col-md-3"><i class="fa-solid fa-certificate text-maroon fs-3 mb-2"></i><div class="small fw-semibold">{{ setting('iso_certified') ? 'ISO Certified' : 'Since '.setting('established_year', '2008') }}</div></div>
                        <div class="col-6 col-md-3"><i class="fa-solid fa-helmet-safety text-maroon fs-3 mb-2"></i><div class="small fw-semibold">Custom Fabrication</div></div>
                        <div class="col-6 col-md-3"><i class="fa-solid fa-screwdriver-wrench text-maroon fs-3 mb-2"></i><div class="small fw-semibold">Technical Service</div></div>
                        <div class="col-6 col-md-3"><i class="fa-solid fa-truck-fast text-maroon fs-3 mb-2"></i><div class="small fw-semibold">Air &amp; Surface Delivery</div></div>
                    </div>
                </div>
            </div>

            <aside class="col-lg-4">
                <div class="position-sticky" style="top: 110px">
                    @include('products.partials.enquiry-sidebar', ['subject' => $product->name, 'productSlug' => $product->slug])
                </div>
            </aside>
        </div>

        @if ($related->isNotEmpty())
            <div class="mt-5 pt-4">
                <x-section-heading eyebrow="Related" title='Related <span class="hl">Products</span>' />
                <div class="row g-4">
                    @foreach ($related as $i => $item)
                        <div class="col-md-6 col-lg-4 reveal" data-delay="{{ $i + 1 }}"><x-product-card :product="$item" /></div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</section>
@endsection
