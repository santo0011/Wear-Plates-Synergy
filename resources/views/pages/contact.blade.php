@extends('layouts.app')

@section('title', 'Contact Us')
@section('meta_description', (string) (setting('seo_contact_description')))
@section('canonical', route('contact'))

@section('content')
<x-page-hero title="Contact Us" subtitle="Send us your enquiry, call us, or message us on WhatsApp." :breadcrumbs="['Contact Us' => null]" />

<section class="section">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-5 reveal reveal-left">@include('partials.contact-info')</div>
            <div class="col-lg-7 reveal reveal-right"><x-enquiry-form :product="$product" /></div>
        </div>
    </div>
</section>

@if ($mapUrl = setting('google_map_embed'))
    <section class="section pt-0">
        <div class="container">
            <div class="map-embed reveal">
                <iframe src="{{ $mapUrl }}" loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="Location map" allowfullscreen></iframe>
            </div>
        </div>
    </section>
@endif

<section class="section bg-light-gray">
    <div class="container">
        <x-section-heading eyebrow="Our Network" title='Offices <span class="hl">Across India</span>' />
        <div class="row g-3">
            @foreach ($locations as $i => $loc)
                <div class="col-sm-6 col-lg-4 col-xl-3 reveal" data-delay="{{ ($i % 4) + 1 }}">
                    <div class="loc-card {{ $loc->type }}">
                        <span class="loc-card-icon"><i class="fa-solid {{ $loc->type === 'plant' ? 'fa-industry' : ($loc->type === 'corporate' ? 'fa-building' : 'fa-location-dot') }}"></i></span>
                        <div><span class="loc-type">{{ $loc->type_label }}</span><h4>{{ $loc->city }}</h4><small>{{ $loc->state }}</small></div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endsection
