@extends('layouts.app')

@php
    $hero = cms('quality.hero');
    $commit = cms('quality.commitment');
    $certs = cms('quality.certificates');
    $qap = cms('quality.qap');
    $testing = cms('quality.testing');
@endphp

@section('title', plain_text($hero->get('title', 'Quality & Certifications')))
@section('meta_description', (string) ($hero->subtitle))
@section('canonical', route('quality'))

@section('content')
<x-page-hero :section="$hero" :crumbs="['Capabilities' => route('infrastructure')]" />

@if ($commit->active())
<section class="section" id="sec-commitment">
    <div class="container-wide">
        <div class="row g-5 align-items-center">
            <div class="col-lg-7 reveal reveal-left">
                <x-section-heading :eyebrow="$commit->eyebrow" :heading="$commit->heading" />
                <div class="prose">{{ rich_text($commit->text) }}</div>
                <ul class="tick-list two-col mt-4">
                    @foreach ($commit->lines('points') as $p)<li>{{ $p }}</li>@endforeach
                </ul>
            </div>
            <div class="col-lg-5 reveal reveal-right">
                <div class="position-relative">
                    <x-media :src="$commit->image" alt="Quality inspection" icon="fa-solid fa-medal" class="ratio-4x5 rounded-media" />
                    <div class="iso-seal">
                        <i class="fa-solid fa-certificate"></i>
                        <strong>{{ setting('iso_label', 'ISO 9001:2015') }}</strong>
                        <span>Certified QMS</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endif

@if ($certs->active() && $certs->items('items'))
<section class="section bg-soft" id="sec-certificates">
    <div class="container-wide">
        <x-section-heading :eyebrow="$certs->eyebrow" :heading="$certs->heading" :text="$certs->text" :center="true" class="mb-5" />
        <div class="row g-4">
            @foreach ($certs->items('items') as $c)
                <div class="col-md-6 col-xl-3 reveal" data-delay="{{ $loop->index }}">
                    <article class="cert-card">
                        @if (! empty($c['image']))
                            <a href="{{ asset($c['image']) }}" target="_blank" rel="noopener" class="cert-scan" aria-label="View {{ $c['title'] ?? 'certificate' }}">
                                <img src="{{ asset($c['image']) }}" alt="{{ $c['title'] ?? '' }}" loading="lazy">
                                <span class="zoom"><i class="fa-solid fa-magnifying-glass-plus"></i></span>
                            </a>
                        @else
                            <div class="cert-scan is-empty"><i class="fa-solid fa-file-shield" aria-hidden="true"></i></div>
                        @endif
                        <div class="cert-body">
                            <h3>{{ $c['title'] ?? '' }}</h3>
                            <dl>
                                @if (! empty($c['issuer']))<div><dt>Issued by</dt><dd>{{ $c['issuer'] }}</dd></div>@endif
                                @if (! empty($c['number']))<div><dt>No.</dt><dd>{{ $c['number'] }}</dd></div>@endif
                                @if (! empty($c['validity']))<div><dt>Validity</dt><dd>{{ $c['validity'] }}</dd></div>@endif
                            </dl>
                            @if (! empty($c['scope']))<p>{{ $c['scope'] }}</p>@endif
                        </div>
                    </article>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

@if ($qap->active() && $qap->items('steps'))
<section class="section" id="sec-qap">
    <div class="container-wide">
        <x-section-heading :eyebrow="$qap->eyebrow" :heading="$qap->heading" :text="$qap->text" class="mb-5" />
        <ol class="qap-flow">
            @foreach ($qap->items('steps') as $s)
                <li class="reveal" data-delay="{{ $loop->index % 3 }}">
                    <span class="qap-num">{{ $loop->iteration }}</span>
                    <div class="qap-card">
                        <span class="tag">{{ $s['stage'] ?? '' }}</span>
                        <h3>{{ $s['title'] ?? '' }}</h3>
                        <p>{{ $s['norm'] ?? '' }}</p>
                        @if (! empty($s['record']))<span class="qap-record"><i class="fa-regular fa-file-lines me-1"></i>{{ $s['record'] }}</span>@endif
                    </div>
                </li>
            @endforeach
        </ol>
        @if ($qap->note)<p class="table-note mt-4"><i class="fa-solid fa-circle-info me-2"></i>{{ $qap->note }}</p>@endif
    </div>
</section>
@endif

@if ($testing->active())
<section class="section bg-dark-steel" id="sec-testing">
    <div class="container-wide">
        <div class="row g-5 align-items-center">
            <div class="col-lg-5">
                <x-section-heading :eyebrow="$testing->eyebrow" :heading="$testing->heading" :text="$testing->text" :light="true" />
                <x-media :src="$testing->image" alt="Testing laboratory" icon="fa-solid fa-flask" class="ratio-16x9 rounded-media mt-3" />
            </div>
            <div class="col-lg-7">
                <div class="row g-3">
                    @foreach ($testing->items('items') as $t)
                        <div class="col-sm-6 reveal" data-delay="{{ $loop->index % 2 }}">
                            <div class="test-card">
                                <i class="{{ $t['icon'] ?? 'fa-solid fa-vial' }}" aria-hidden="true"></i>
                                <h3>{{ $t['title'] ?? '' }}</h3>
                                <p>{{ $t['text'] ?? '' }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>
@endif
@endsection
