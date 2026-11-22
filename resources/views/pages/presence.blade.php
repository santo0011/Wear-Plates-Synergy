@extends('layouts.app')

@section('title', 'Our Presence Across India')
@section('meta_description', (string) (setting('seo_presence_description')))

@section('content')
@php
    $corporate = $locations->where('type', 'corporate');
    $plants = $locations->where('type', 'plant');
    $regional = $locations->where('type', 'regional');
@endphp

<x-page-hero title="Our Presence Across India" subtitle="Corporate office, manufacturing plant and regional offices serving customers across the country." :breadcrumbs="['Our Presence' => null]" />

<section class="section">
    <div class="container">
        <div class="row g-4 mb-5">
            @foreach ($corporate as $loc)
                <div class="col-md-6 reveal reveal-left">
                    <div class="hq-card">
                        <i class="fa-solid fa-building big"></i>
                        <span class="label">Corporate Office</span>
                        <h3>{{ $loc->city }}</h3>
                        <div>{{ $loc->state }}</div>
                        @if ($loc->address)<div class="mt-2 small">{!! nl2br(e($loc->address)) !!}</div>@endif
                        @if ($loc->phone)<div class="mt-2"><a class="text-white" href="{{ tel_url($loc->phone) }}"><i class="fa-solid fa-phone me-1"></i>{{ $loc->phone }}</a></div>@endif
                    </div>
                </div>
            @endforeach
            @foreach ($plants as $loc)
                <div class="col-md-6 reveal reveal-right">
                    <div class="hq-card gold">
                        <i class="fa-solid fa-industry big"></i>
                        <span class="label">Manufacturing Plant</span>
                        <h3>{{ $loc->city }}</h3>
                        <div>{{ $loc->state }}</div>
                        @if ($loc->address)<div class="mt-2 small">{!! nl2br(e($loc->address)) !!}</div>@endif
                        @if ($loc->phone)<div class="mt-2"><a href="{{ tel_url($loc->phone) }}" class="text-reset"><i class="fa-solid fa-phone me-1"></i>{{ $loc->phone }}</a></div>@endif
                    </div>
                </div>
            @endforeach
        </div>

        <div class="row g-5 align-items-start">
            <div class="col-lg-7 reveal">
                <div class="presence-wrap"><x-india-map :locations="$locations" /></div>
            </div>
            <div class="col-lg-5">
                <span class="eyebrow">Regional Offices</span>
                <h2 class="section-title fs-1">{{ $regional->count() }} Regional Offices</h2>
                <p class="section-sub mb-4">Hover a location to highlight it on the map.</p>
                <div class="row g-3">
                    @foreach ($regional as $i => $loc)
                        <div class="col-sm-6 col-lg-12 col-xl-6 reveal" data-delay="{{ ($i % 4) + 1 }}">
                            <div class="loc-card" data-loc="{{ $loc->id }}" tabindex="0">
                                <span class="loc-card-icon"><i class="fa-solid fa-location-dot"></i></span>
                                <div>
                                    <h4>{{ $loc->city }}</h4>
                                    <small>{{ $loc->state }}</small>
                                    @if ($loc->address)<small class="mt-1">{{ $loc->address }}</small>@endif
                                    @if ($loc->phone)<small><a href="{{ tel_url($loc->phone) }}">{{ $loc->phone }}</a></small>@endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section bg-light-gray">
    <div class="container"><x-delivery-band /></div>
</section>

<section class="section">
    <div class="container"><x-cta-band title="Looking for Support Near You?" text="Contact us and we will connect you with the nearest office." /></div>
</section>
@endsection
