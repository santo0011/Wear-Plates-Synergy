@extends('layouts.app')

@section('title', 'About Us')
@section('meta_description', (string) (setting('seo_about_description')))

@section('content')
@php
    $year = (int) setting('established_year', 2008);
    $aboutImage = setting('about_image');
@endphp

<x-page-hero title="About Us" :subtitle="setting('tagline')" :breadcrumbs="['About Us' => null]" />

<section class="section">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6 reveal reveal-left">
                <div class="position-relative">
                    @if ($aboutImage)
                        <div class="intro-visual"><img src="{{ asset($aboutImage) }}" alt="{{ setting('site_name') }}" loading="lazy"></div>
                    @else
                        <div class="intro-mosaic">
                            @foreach ($categories->take(4) as $cat)
                                <div class="tile">@if ($cat->image_url)<img src="{{ $cat->image_url }}" alt="{{ $cat->name }}" loading="lazy" width="800" height="560">@endif</div>
                            @endforeach
                        </div>
                    @endif
                    <div class="exp-badge"><strong>{{ $year }}</strong><span>Since</span></div>
                </div>
            </div>
            <div class="col-lg-6 reveal reveal-right">
                <span class="eyebrow">Who We Are</span>
                <h2 class="section-title">{{ setting('about_heading', 'The Leader in Wear Protection') }}</h2>
                <p class="fs-6">{!! nl2br(e(setting('about_text'))) !!}</p>
                @if ($tag = setting('about_tagline'))
                    <div class="quote-tagline my-4">&ldquo;{{ $tag }}&rdquo;</div>
                @endif
                <div class="row g-3">
                    <div class="col-sm-6"><div class="info-panel h-100"><i class="fa-solid fa-calendar-check text-maroon fs-4 mb-2 d-block"></i><strong class="d-block">Serving Since {{ $year }}</strong></div></div>
                    @if (setting('iso_certified') || setting('msme_registered'))
                        <div class="col-sm-6"><div class="info-panel h-100"><i class="fa-solid fa-certificate text-maroon fs-4 mb-2 d-block"></i><strong class="d-block">{{ collect([setting('iso_certified') ? 'ISO Certified' : null, setting('msme_registered') ? 'MSME Registered' : null])->filter()->join(' & ') }}</strong></div></div>
                    @endif
                    @if ($corp = setting('corporate_office'))
                        <div class="col-sm-6"><div class="info-panel h-100"><i class="fa-solid fa-building text-maroon fs-4 mb-2 d-block"></i><strong class="d-block">Corporate Office</strong><span class="text-muted">{{ $corp }}</span></div></div>
                    @endif
                    @if ($plant = setting('plant_location'))
                        <div class="col-sm-6"><div class="info-panel h-100"><i class="fa-solid fa-industry text-maroon fs-4 mb-2 d-block"></i><strong class="d-block">Manufacturing Plant</strong><span class="text-muted">{{ $plant }}</span></div></div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section bg-light-gray">
    <div class="container">
        <x-section-heading eyebrow="What We Offer" title='Our <span class="hl">Range</span>' subtitle="Wear protection products and solutions across six categories." />
        <div class="row g-3">
            @foreach ($categories as $i => $cat)
                <div class="col-md-6 col-lg-4 reveal" data-delay="{{ ($i % 3) + 1 }}">
                    <a href="{{ route('products.category', $cat) }}" class="loc-card">
                        <span class="loc-card-icon"><i class="{{ $cat->icon_class }}"></i></span>
                        <div><h4 class="fs-6">{{ $cat->name }}</h4><small>View solutions <i class="fa-solid fa-arrow-right-long ms-1"></i></small></div>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</section>

<section class="section">
    <div class="container"><x-md-message /></div>
</section>

<section class="section pt-0">
    <div class="container"><x-cta-band /></div>
</section>
@endsection
