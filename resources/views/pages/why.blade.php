@extends('layouts.app')

@php
    $hero = cms('why.hero');
    $reasons = cms('why.reasons');
    $promise = cms('why.promise');
    $ve = cms('home.value_engineering');
@endphp

@section('title', plain_text($hero->get('title', 'Why Choose Us')))
@section('meta_description', (string) (setting('seo_why_description') ?: $hero->subtitle))
@section('canonical', route('why'))

@section('content')
<x-page-hero :section="$hero" :crumbs="['Company' => route('about')]" />

@if ($reasons->active() && $reasons->items('items'))
<section class="section" id="sec-reasons">
    <div class="container-wide">
        <x-section-heading :eyebrow="$reasons->eyebrow" :heading="$reasons->heading" :text="$reasons->text" :center="true" class="mb-5" />
        <div class="reason-grid">
            @foreach ($reasons->items('items') as $r)
                <div class="reason-card reveal" data-delay="{{ $loop->index % 4 }}">
                    <span class="reason-num">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                    <span class="feature-ico"><i class="{{ $r['icon'] ?? 'fa-solid fa-check' }}" aria-hidden="true"></i></span>
                    <h3>{{ $r['title'] ?? '' }}</h3>
                    <p>{{ $r['text'] ?? '' }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

@if ($ve->active())
<section class="ve-band" @if ($ve->image) style="--ve-img:url('{{ asset($ve->image) }}')" @endif>
    <div class="container-wide position-relative">
        <div class="row g-5 align-items-center">
            <div class="col-lg-5">
                <x-section-heading :eyebrow="$ve->eyebrow" :heading="$ve->heading" :light="true" />
                <div class="prose is-light">{{ rich_text($ve->text) }}</div>
            </div>
            <div class="col-lg-7">
                <ol class="ve-steps">
                    @foreach ($ve->items('steps') as $step)
                        <li class="reveal" data-delay="{{ $loop->index }}">
                            <span class="ve-num">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                            <i class="{{ $step['icon'] ?? 'fa-solid fa-check' }} ve-ico" aria-hidden="true"></i>
                            <h3>{{ $step['title'] ?? '' }}</h3>
                            <p>{{ $step['text'] ?? '' }}</p>
                        </li>
                    @endforeach
                </ol>
            </div>
        </div>
    </div>
</section>
@endif

@if ($promise->active())
<section class="section" id="sec-promise">
    <div class="container-wide">
        <div class="row g-5 align-items-center">
            <div class="col-lg-6 reveal reveal-left">
                <x-section-heading :eyebrow="$promise->eyebrow" :heading="$promise->heading" :text="$promise->text" />
                <ul class="promise-list">
                    @foreach ($promise->lines('points') as $p)<li><i class="fa-solid fa-circle-check"></i>{{ $p }}</li>@endforeach
                </ul>
            </div>
            <div class="col-lg-6 reveal reveal-right">
                <x-media :src="$promise->image" alt="Dispatch from our plant" icon="fa-solid fa-truck-fast" class="ratio-4x3 rounded-media" />
            </div>
        </div>
    </div>
</section>
@endif

<x-delivery-band />
@endsection
