@extends('layouts.app')

@php
    $hero = cms('infrastructure.hero');
    $overview = cms('infrastructure.overview');
    $capacity = cms('infrastructure.capacity');
    $facilities = cms('infrastructure.facilities');
    $process = cms('infrastructure.process');
    $machines = cms('infrastructure.machines');
    $design = cms('infrastructure.design');
    $expansion = cms('infrastructure.expansion');
    $gallery = cms('infrastructure.gallery');
@endphp

@section('title', plain_text($hero->get('title', 'Infrastructure')))
@section('meta_description', (string) ($hero->subtitle))
@section('canonical', route('infrastructure'))

@section('content')
<x-page-hero :section="$hero" :crumbs="['Capabilities' => route('infrastructure')]" />

@if ($overview->active())
<section class="section" id="sec-overview">
    <div class="container-wide">
        <div class="row g-5 align-items-center">
            <div class="col-lg-6 reveal reveal-left">
                <x-media :src="$overview->image" alt="Manufacturing plant at GIDC Ankleshwar" icon="fa-solid fa-industry" class="ratio-4x3 rounded-media" />
            </div>
            <div class="col-lg-6 reveal reveal-right">
                <x-section-heading :eyebrow="$overview->eyebrow" :heading="$overview->heading" />
                <div class="prose">{{ rich_text($overview->text) }}</div>
            </div>
        </div>
    </div>
</section>
@endif

@if ($capacity->active() && $capacity->items('items'))
<section class="stats-band" id="sec-capacity">
    <div class="container-wide">
        <x-section-heading :eyebrow="$capacity->eyebrow" :heading="$capacity->heading" :light="true" :center="true" class="mb-5" />
        <div class="stats-grid">
            @foreach ($capacity->items('items') as $item)
                @php $num = preg_replace('/\D/', '', (string) ($item['value'] ?? '')); @endphp
                <div class="stat-cell reveal" data-delay="{{ min($loop->index, 5) }}">
                    <i class="{{ $item['icon'] ?? 'fa-solid fa-chart-simple' }} stat-ico" aria-hidden="true"></i>
                    <div class="stat-num">@if ($num !== '')<span data-count="{{ $num }}">{{ number_format((int) $num) }}</span>@else<span>{{ $item['value'] ?? '' }}</span>@endif<em>{{ $item['suffix'] ?? '' }}</em></div>
                    <div class="stat-lbl">{{ $item['label'] ?? '' }}</div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

@if ($facilities->active() && $facilities->items('items'))
<section class="section" id="sec-facilities">
    <div class="container-wide">
        <x-section-heading :eyebrow="$facilities->eyebrow" :heading="$facilities->heading" :text="$facilities->text" :center="true" class="mb-5" />
        <div class="feature-grid">
            @foreach ($facilities->items('items') as $f)
                <div class="feature-card reveal" data-delay="{{ $loop->index % 4 }}">
                    <span class="feature-ico"><i class="{{ $f['icon'] ?? 'fa-solid fa-check' }}" aria-hidden="true"></i></span>
                    <h3>{{ $f['title'] ?? '' }}</h3>
                    <p>{{ $f['text'] ?? '' }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

@if ($process->active())
<section class="section bg-soft" id="sec-process">
    <div class="container-wide">
        <x-section-heading :eyebrow="$process->eyebrow" :heading="$process->heading" :text="$process->text" class="mb-5" />
        <div class="row g-4">
            @foreach ($process->items('steps') as $step)
                <div class="col-md-6 col-xl-3 reveal" data-delay="{{ $loop->index }}">
                    <div class="step-card">
                        <x-media :src="$step['image'] ?? null" :alt="$step['title'] ?? ''" icon="fa-solid fa-robot" class="ratio-16x9" />
                        <div class="step-body">
                            <span class="step-num">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                            <h3>{{ $step['title'] ?? '' }}</h3>
                            <p>{{ $step['text'] ?? '' }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

@if ($machines->active() && count($machines->table('table')) > 1)
<section class="section" id="sec-machines">
    <div class="container-wide">
        <x-section-heading :eyebrow="$machines->eyebrow" :heading="$machines->heading" :text="$machines->text" class="mb-4" />
        @include('partials.data-table', ['rows' => $machines->table('table'), 'class' => 'reveal is-machines'])
    </div>
</section>
@endif

@if ($design->active())
<section class="section bg-dark-steel" id="sec-design">
    <div class="container-wide">
        <div class="row g-5 align-items-center">
            <div class="col-lg-4">
                <x-section-heading :eyebrow="$design->eyebrow" :heading="$design->heading" :text="$design->text" :light="true" />
            </div>
            <div class="col-lg-8">
                <div class="row g-3">
                    @foreach ($design->items('drawings') as $d)
                        <div class="col-sm-4 reveal" data-delay="{{ $loop->index }}">
                            <figure class="drawing-card">
                                <x-media :src="$d['image'] ?? null" :alt="$d['title'] ?? ''" icon="fa-solid fa-compass-drafting" class="ratio-3x4" fit="contain" />
                                <figcaption>{{ $d['title'] ?? '' }}</figcaption>
                            </figure>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>
@endif

@if ($expansion->active())
<section class="section" id="sec-expansion">
    <div class="container-wide">
        <div class="expansion-card reveal">
            <div class="expansion-text">
                <x-section-heading :eyebrow="$expansion->eyebrow" :heading="$expansion->heading" :text="$expansion->text" />
                <ul class="tick-list two-col">
                    @foreach ($expansion->lines('points') as $p)<li>{{ $p }}</li>@endforeach
                </ul>
            </div>
            <x-media :src="$expansion->image" alt="Capacity expansion" icon="fa-solid fa-rocket" class="expansion-img" />
        </div>
    </div>
</section>
@endif

@if ($gallery->active() && $gallery->items('photos'))
<section class="section bg-soft" id="sec-gallery">
    <div class="container-wide">
        <x-section-heading :eyebrow="$gallery->eyebrow" :heading="$gallery->heading" :center="true" class="mb-5" />
        <div class="gallery-grid">
            @foreach ($gallery->items('photos') as $photo)
                <figure class="gallery-item reveal" data-delay="{{ $loop->index % 3 }}">
                    <x-media :src="$photo['image'] ?? null" :alt="$photo['caption'] ?? ''" icon="fa-solid fa-camera" class="ratio-4x3" />
                    @if (! empty($photo['caption']))<figcaption>{{ $photo['caption'] }}</figcaption>@endif
                </figure>
            @endforeach
        </div>
    </div>
</section>
@endif
@endsection
