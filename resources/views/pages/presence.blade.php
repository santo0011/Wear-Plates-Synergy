@extends('layouts.app')

@php
    $hero = cms('presence.hero');
    $intro = cms('presence.intro');
    $corporate = $locations->where('type', 'corporate');
    $plants = $locations->where('type', 'plant');
    $regional = $locations->where('type', 'regional');
@endphp

@section('title', plain_text($hero->get('title', 'Our Presence')))
@section('meta_description', (string) (setting('seo_presence_description') ?: $hero->subtitle))
@section('canonical', route('presence'))

@section('content')
<x-page-hero :section="$hero" :crumbs="['Company' => route('about')]" />

<section class="section" id="sec-intro">
    <div class="container-wide">
        @if ($intro->active())
            <x-section-heading :eyebrow="$intro->eyebrow" :heading="$intro->heading" :text="$intro->text" :center="true" class="mb-5" />
        @endif

        <div class="row g-4 mb-5">
            @foreach ($corporate->concat($plants) as $loc)
                <div class="col-md-6 reveal" data-delay="{{ $loop->index }}">
                    <div @class(['hq-card', 'is-plant' => $loc->type === 'plant']) data-loc="{{ $loc->id }}">
                        <i class="fa-solid {{ $loc->type === 'plant' ? 'fa-industry' : 'fa-building' }} hq-ico" aria-hidden="true"></i>
                        <span class="hq-label">{{ $loc->type_label }}</span>
                        <h2>{{ $loc->city }}</h2>
                        <span class="hq-state">{{ $loc->state }}</span>
                        @if ($loc->address)<p>{{ $loc->address }}</p>@endif
                        @if ($loc->phone)<a href="{{ tel_url($loc->phone) }}"><i class="fa-solid fa-phone me-1"></i>{{ $loc->phone }}</a>@endif
                    </div>
                </div>
            @endforeach
        </div>

        <div class="row g-5 align-items-start">
            <div class="col-lg-7 reveal">
                <div class="map-panel"><x-india-map :locations="$locations" /></div>
            </div>
            <div class="col-lg-5">
                <h2 class="sub-title">{{ $regional->count() }} Regional Offices</h2>
                <p class="text-muted mb-4">Hover a location to highlight it on the map.</p>
                <div class="loc-grid">
                    @foreach ($regional as $loc)
                        <div class="loc-card" data-loc="{{ $loc->id }}" tabindex="0">
                            <span class="loc-ico"><i class="fa-solid fa-location-dot"></i></span>
                            <div>
                                <strong>{{ $loc->city }}</strong>
                                <small>{{ $loc->state }}</small>
                                @if ($loc->address)<small class="loc-addr">{{ $loc->address }}</small>@endif
                            </div>
                        </div>
                    @endforeach
                </div>
                @if ($network = $intro->lines('network'))
                    <h3 class="sub-title-sm mt-5">Service Network</h3>
                    <div class="city-tags">
                        @foreach ($network as $city)<span>{{ $city }}</span>@endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>

<x-delivery-band />
@endsection
