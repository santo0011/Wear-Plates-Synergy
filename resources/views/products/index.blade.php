@extends('layouts.app')

@php
    $hero = cms('products.hero');
    $intro = cms('products.intro');
    $spectrum = cms('products.spectrum');
@endphp

@section('title', plain_text($hero->get('title', 'Products & Solutions')))
@section('meta_description', (string) (setting('seo_products_description') ?: $hero->subtitle))
@section('canonical', route('products.index'))

@section('content')
<x-page-hero :section="$hero" />

@if ($intro->active())
<section class="section-sm" id="sec-intro">
    <div class="container-wide">
        <div class="row g-4 align-items-end">
            <div class="col-lg-8">
                <x-section-heading :eyebrow="$intro->eyebrow" :heading="$intro->heading" :text="$intro->text" class="mb-0" />
            </div>
            @if ($modes = cms_pairs($intro->wear_modes))
                <div class="col-lg-4">
                    <div class="legend-card">
                        <span class="legend-title">Wear mode legend</span>
                        <ul>
                            @foreach ($modes as [$code, $meaning])
                                <li><b>{{ $code }}</b>{{ $meaning }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif
        </div>
    </div>
</section>
@endif

<nav class="cat-jump" aria-label="Product categories">
    <div class="container-wide">
        <div class="cat-jump-inner">
            @foreach ($categories as $category)
                <a href="#cat-{{ $category->slug }}"><i class="{{ $category->icon_class }}"></i>{{ $category->name }}</a>
            @endforeach
        </div>
    </div>
</nav>

@foreach ($categories as $category)
    <section @class(['section', 'bg-soft' => $loop->even]) id="cat-{{ $category->slug }}">
        <div class="container-wide">
            <div class="sec-head-row">
                <div class="sec-head">
                    <span class="eyebrow"><i class="{{ $category->icon_class }} me-1"></i>Category {{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                    <h2 class="sec-title">{{ $category->name }}</h2>
                    <div class="sec-lead"><p>{{ $category->short_description }}</p></div>
                </div>
                <a href="{{ route('products.category', $category) }}" class="btn btn-outline-brand">Category Overview<i class="fa-solid fa-arrow-right ms-2"></i></a>
            </div>
            <div class="row g-4">
                @foreach ($category->activeProducts as $product)
                    @php $product->setRelation('category', $category); @endphp
                    <div class="col-sm-6 col-lg-4 col-xxl-3 reveal" data-delay="{{ $loop->index % 4 }}">
                        @include('products.partials.product-card', ['product' => $product])
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endforeach

@if ($spectrum->active())
<section class="section-sm" id="sec-spectrum">
    <div class="container-wide">
        <div class="note-panel">
            <span class="note-icon"><i class="fa-solid fa-atom"></i></span>
            <div>
                <x-section-heading :eyebrow="$spectrum->eyebrow" :heading="$spectrum->heading" :text="$spectrum->text" class="mb-0" />
            </div>
        </div>
    </div>
</section>
@endif
@endsection
