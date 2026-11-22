@extends('layouts.app')

@section('canonical', route('home'))

@section('content')
@php
    $year = (int) setting('established_year', 2008);
    $heroImage = setting('hero_image');
    $regionalCount = $locations->where('type', 'regional')->count();
@endphp

{{-- ================= HERO ================= --}}
@php
    $heroTitle = setting('hero_title', 'WEAR PLATES SYNERGY VE INDIA');
    $hasAccent = \Illuminate\Support\Str::contains($heroTitle, 'VE INDIA');
    $trust = array_values(array_filter([
        'Since '.$year,
        setting('iso_certified') ? 'ISO Certified' : null,
        setting('msme_registered') ? 'MSME Registered' : null,
    ]));
@endphp
<section class="hero2 {{ $heroImage ? 'has-image' : '' }}" @if ($heroImage) style="--hero-img:url('{{ asset($heroImage) }}')" @endif>
    <div class="hero2-bg" aria-hidden="true">
        <span class="hero2-blob b1"></span>
        <span class="hero2-blob b2"></span>
    </div>
    <div class="container position-relative">
        <div class="row align-items-center g-5">
            <div class="col-lg-7 text-center text-lg-start">
                <p class="hero2-eyebrow h-anim" style="--d:.1s">Wear Protection Specialists</p>
                <h1 class="hero2-title">
                    @if ($hasAccent)
                        <span class="line"><span style="--d:.25s">{{ trim(\Illuminate\Support\Str::before($heroTitle, 'VE INDIA')) }}</span></span>
                        <span class="line"><span class="accent" style="--d:.4s">VE India</span></span>
                    @else
                        <span class="line"><span style="--d:.25s">{{ $heroTitle }}</span></span>
                    @endif
                </h1>
                <p class="hero2-tagline h-anim" style="--d:.6s">{{ setting('hero_tagline', setting('tagline')) }}</p>
                <p class="hero2-lead h-anim" style="--d:.75s">{{ setting('hero_text') }}</p>
                <div class="d-flex flex-wrap gap-3 justify-content-center justify-content-lg-start h-anim" style="--d:.9s">
                    <a href="{{ route('products.index') }}" class="btn btn-gold btn-lg">Explore Our Products <i class="fa-solid fa-arrow-right ms-2"></i></a>
                    <a href="{{ route('contact') }}" class="btn btn-outline-light-gold btn-lg">Contact Us</a>
                </div>
                <ul class="hero2-trust h-anim" style="--d:1.05s">
                    @foreach ($trust as $item)
                        <li><i class="fa-solid fa-circle-check"></i>{{ $item }}</li>
                    @endforeach
                </ul>
            </div>
            <div class="col-lg-5">
                <div class="hero2-visual h-anim-zoom" style="--d:.35s">
                    <span class="ring r1" aria-hidden="true"></span>
                    <span class="ring r2" aria-hidden="true"></span>
                    <span class="ring r3" aria-hidden="true"><i></i></span>
                    <span class="glow" aria-hidden="true"></span>
                    <img src="{{ asset(setting('logo') ?: 'images/brand/logo.png') }}" alt="{{ setting('site_name', config('app.name')) }} logo" width="512" height="512" fetchpriority="high">
                </div>
            </div>
        </div>
    </div>
    <div class="hero-wave" aria-hidden="true">
        <svg viewBox="0 0 1440 70" preserveAspectRatio="none"><path d="M0 70V40C240 10 480 0 720 18S1200 60 1440 30V70Z" fill="#fff"/></svg>
    </div>
</section>

{{-- ================= STATS ================= --}}
<section class="stats-strip">
    <div class="container">
        @php
            $stats = [
                ['fa-calendar-days', $year, 'Established', ['data-count' => $year, 'data-from' => max(0, $year - 18)]],
                ['fa-boxes-stacked', $categories->count(), 'Product Categories', ['data-count' => $categories->count()]],
                ['fa-map-location-dot', $regionalCount, 'Regional Offices', ['data-count' => $regionalCount]],
                ['fa-industry', 'GIDC', 'Ankleshwar Plant', []],
            ];
        @endphp
        <div class="stats-card reveal">
            <div class="row g-2">
                @foreach ($stats as $i => [$icon, $value, $label, $count])
                    <div class="col-6 col-lg-3">
                        <div class="stat" style="--i: {{ $i }}">
                            <div class="stat-icon"><i class="fa-solid {{ $icon }}"></i></div>
                            <div>
                                @if ($count)
                                    <div class="stat-value" data-delay="{{ 250 + $i * 150 }}" @foreach ($count as $attr => $v) {{ $attr }}="{{ $v }}" @endforeach>{{ $value }}</div>
                                @else
                                    <div class="stat-value stat-letters" aria-label="{{ $value }}">@foreach (mb_str_split((string) $value) as $c => $ch)<span style="--c: {{ $c }}" aria-hidden="true">{{ $ch }}</span>@endforeach</div>
                                @endif
                                <div class="stat-label">{{ $label }}</div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

{{-- ================= INTRO ================= --}}
<section class="section">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6 reveal reveal-left">
                <div class="position-relative">
                    <div class="intro-mosaic">
                        @foreach ($categories->take(4) as $cat)
                            <div class="tile">@if ($cat->image_url)<img src="{{ $cat->image_url }}" alt="{{ $cat->name }}" loading="lazy" width="800" height="560">@endif</div>
                        @endforeach
                    </div>
                    <div class="exp-badge"><strong>{{ $year }}</strong><span>Since</span></div>
                </div>
            </div>
            <div class="col-lg-6 reveal reveal-right">
                <span class="eyebrow">Who We Are</span>
                <h2 class="section-title">{{ setting('intro_heading', 'Complete Wear Protection Solutions') }}</h2>
                <p class="fs-6">{{ setting('intro_text') }}</p>
                <ul class="check-list two-col">
                    <li><i class="fa-solid fa-check"></i>Wear Liners &amp; Plates</li>
                    <li><i class="fa-solid fa-check"></i>Hardox Fabrications</li>
                    <li><i class="fa-solid fa-check"></i>Ceramics &amp; Tungsten Carbide</li>
                    <li><i class="fa-solid fa-check"></i>Alloys Castings</li>
                    <li><i class="fa-solid fa-check"></i>Welding Electrodes</li>
                    <li><i class="fa-solid fa-check"></i>High-Temperature Coatings</li>
                </ul>
                @if ($tag = setting('about_tagline'))
                    <div class="quote-tagline mb-4">&ldquo;{{ $tag }}&rdquo;</div>
                @endif
                <a href="{{ route('about') }}" class="btn btn-maroon btn-lg">More About Us <i class="fa-solid fa-arrow-right ms-2"></i></a>
            </div>
        </div>
    </div>
</section>

{{-- ================= PRODUCT CATEGORIES ================= --}}
<section class="section bg-light-gray">
    <div class="container">
        <x-section-heading eyebrow="Products & Solutions" title='Our <span class="hl">Product</span> Categories'
            subtitle="A complete range of wear protection products and solutions for industry." />
        <div class="row g-4">
            @foreach ($categories as $i => $category)
                <div class="col-md-6 col-lg-4 reveal" data-delay="{{ ($i % 3) + 1 }}">
                    <x-category-card :category="$category" :index="$i + 1" />
                </div>
            @endforeach
        </div>
        <div class="text-center mt-5 reveal">
            <a href="{{ route('products.index') }}" class="btn btn-outline-maroon btn-lg">View All Products <i class="fa-solid fa-arrow-right ms-2"></i></a>
        </div>
    </div>
</section>

{{-- ================= WHY CHOOSE US ================= --}}
<section class="section section-dark">
    <div class="container">
        <x-section-heading eyebrow="Why Choose Us" :title="e(setting('why_heading', 'Why Choose Us'))" :subtitle="setting('why_intro')" />
        <x-why-grid />
    </div>
</section>

{{-- ================= FEATURED PRODUCTS ================= --}}
@if ($featuredProducts->isNotEmpty())
<section class="section">
    <div class="container">
        <x-section-heading eyebrow="Featured" title='Featured <span class="hl">Solutions</span>'
            subtitle="Enquire online or on WhatsApp for sizes, grades and availability." />
        <div class="row g-4">
            @foreach ($featuredProducts as $i => $product)
                <div class="col-md-6 col-lg-4 reveal" data-delay="{{ ($i % 3) + 1 }}">
                    <x-product-card :product="$product" />
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ================= PRESENCE ================= --}}
<section class="section bg-light-gray">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-5 reveal reveal-left">
                <span class="eyebrow">Our Presence</span>
                <h2 class="section-title">India-Wide <span class="hl">Presence</span></h2>
                <p class="section-sub mb-4">Corporate office in {{ setting('corporate_office', 'Mumbai') }}, manufacturing plant at {{ setting('plant_location', 'GIDC, Ankleshwar, Gujarat') }}, and regional offices across India.</p>
                <div class="row g-3 mb-4">
                    @foreach ($locations->whereIn('type', ['corporate', 'plant']) as $loc)
                        <div class="col-sm-6">
                            <div class="loc-card {{ $loc->type }}" data-loc="{{ $loc->id }}">
                                <span class="loc-card-icon"><i class="fa-solid {{ $loc->type === 'plant' ? 'fa-industry' : 'fa-building' }}"></i></span>
                                <div><span class="loc-type">{{ $loc->type_label }}</span><h4>{{ $loc->city }}</h4></div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="d-flex flex-wrap gap-2 mb-4">
                    @foreach ($locations->where('type', 'regional') as $loc)
                        <span class="product-meta-chip" data-loc="{{ $loc->id }}"><i class="fa-solid fa-location-dot"></i>{{ $loc->city }}</span>
                    @endforeach
                </div>
                <a href="{{ route('presence') }}" class="btn btn-maroon">View All Locations <i class="fa-solid fa-arrow-right ms-2"></i></a>
            </div>
            <div class="col-lg-7 reveal reveal-right">
                <div class="presence-wrap"><x-india-map :locations="$locations" /></div>
            </div>
        </div>
    </div>
</section>

{{-- ================= MD MESSAGE ================= --}}
<section class="section">
    <div class="container"><x-md-message /></div>
</section>

{{-- ================= DELIVERY ================= --}}
<section class="section pt-0">
    <div class="container"><x-delivery-band /></div>
</section>

{{-- ================= CONTACT ================= --}}
<section class="section bg-light-gray">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-5 reveal reveal-left">@include('partials.contact-info')</div>
            <div class="col-lg-7 reveal reveal-right"><x-enquiry-form /></div>
        </div>
    </div>
</section>
@endsection
