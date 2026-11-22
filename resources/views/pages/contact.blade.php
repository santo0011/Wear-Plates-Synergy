@extends('layouts.app')

@php
    $hero = cms('contact.hero');
    $intro = cms('contact.intro');
@endphp

@section('title', plain_text($hero->get('title', 'Contact Us')))
@section('meta_description', (string) (setting('seo_contact_description') ?: $hero->subtitle))
@section('canonical', route('contact'))

@section('content')
<x-page-hero :section="$hero" />

<section class="section" id="sec-intro">
    <div class="container-wide">
        <div class="row g-5">
            <div class="col-lg-5 reveal reveal-left">
                @if ($intro->active())
                    <x-section-heading :eyebrow="$intro->eyebrow" :heading="$intro->heading" :text="$intro->text" />
                @endif
                @include('partials.contact-info')
            </div>
            <div class="col-lg-7 reveal reveal-right"><x-enquiry-form :product="$product" /></div>
        </div>
    </div>
</section>

@if ($intro->active() && $intro->items('offices'))
<section class="section bg-soft">
    <div class="container-wide">
        <x-section-heading eyebrow="Our Offices" heading="Addresses **Across India**" :center="true" class="mb-5" />
        <div class="row g-4">
            @foreach ($intro->items('offices') as $office)
                <div class="col-md-6 col-xl-4 reveal" data-delay="{{ $loop->index % 3 }}">
                    <div class="office-card">
                        <span class="office-ico"><i class="fa-solid {{ \Illuminate\Support\Str::contains(strtolower($office['name'] ?? ''), 'plant') ? 'fa-industry' : 'fa-building' }}"></i></span>
                        <h3>{{ $office['name'] ?? '' }}</h3>
                        <p>{{ $office['address'] ?? '' }}</p>
                        @if (! empty($office['phone']))<a href="{{ tel_url($office['phone']) }}"><i class="fa-solid fa-phone"></i>{{ $office['phone'] }}</a>@endif
                        @if (! empty($office['email']))<a href="mailto:{{ $office['email'] }}"><i class="fa-solid fa-envelope"></i>{{ $office['email'] }}</a>@endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

@if ($mapUrl = setting('google_map_embed'))
    <section class="section pt-0 bg-soft">
        <div class="container-wide">
            <div class="map-embed reveal">
                <iframe src="{{ $mapUrl }}" loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="Location map" allowfullscreen></iframe>
            </div>
        </div>
    </section>
@endif
@endsection
