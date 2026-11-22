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
@php
    $certLogos = array_filter([
        setting('iso_certified') ? [setting('iso_logo'), setting('iso_label', 'ISO 9001:2015').' Certified'] : null,
        setting('make_in_india') ? [setting('make_in_india_logo'), 'Make in India'] : null,
        setting('msme_registered') ? [setting('msme_logo'), 'MSME Registered'] : null,
    ], fn ($c) => $c && $c[0]);
@endphp
<section class="section why-section" id="sec-reasons">
    <div class="container-wide">
        <div class="why-split">
            <aside class="why-intro reveal reveal-left">
                <x-section-heading :eyebrow="$reasons->eyebrow" :heading="$reasons->heading" :text="$reasons->text" :light="true" class="mb-0" />
                @if ($certLogos)
                    <div class="why-certs" aria-label="Certifications">
                        @foreach ($certLogos as [$logo, $label])
                            <span class="why-cert"><img src="{{ asset($logo) }}" alt="{{ $label }}" title="{{ $label }}" loading="lazy"></span>
                        @endforeach
                    </div>
                @endif
                <div class="why-actions">
                    <a href="{{ route('contact') }}#enquiry" class="btn btn-brand">Get a Quote<i class="fa-solid fa-arrow-right ms-2" aria-hidden="true"></i></a>
                    <a href="{{ route('products.index') }}" class="btn btn-link-arrow">Our Products <i class="fa-solid fa-arrow-right"></i></a>
                </div>
            </aside>
            <div class="why-tiles">
                @foreach ($reasons->items('items') as $r)
                    <article class="why-tile reveal" data-delay="{{ $loop->index % 2 }}">
                        <span class="why-tile-ico"><i class="{{ $r['icon'] ?? 'fa-solid fa-check' }}" aria-hidden="true"></i></span>
                        <div class="why-tile-body">
                            <span class="why-tile-num">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                            <h3>{{ $r['title'] ?? '' }}</h3>
                            @if (! empty($r['text']))<p>{{ $r['text'] }}</p>@endif
                        </div>
                    </article>
                @endforeach
            </div>
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
@php
    // "Stock: Ready ..." -> label "Stock" + text. The icon follows the label.
    $promiseIcons = ['stock' => 'fa-solid fa-boxes-stacked', 'delivery' => 'fa-solid fa-truck-fast', 'dispatch' => 'fa-solid fa-dolly'];
    $promisePoints = array_map(function ($p) use ($promiseIcons) {
        [$label, $text] = preg_match('/^([A-Za-z ]{3,20}):\s*(.+)$/u', $p, $m) ? [trim($m[1]), $m[2]] : [null, $p];
        return [$label, $text, $promiseIcons[strtolower((string) $label)] ?? 'fa-solid fa-bolt'];
    }, $promise->lines('points'));
@endphp
<section class="section bg-soft" id="sec-promise">
    <div class="container-wide">
        <div class="row g-5 align-items-center">
            <div class="col-lg-5 reveal reveal-left">
                <div class="promise-media">
                    <x-media :src="$promise->image" alt="Dispatch from our plant" icon="fa-solid fa-truck-fast" class="ratio-4x5 rounded-media" />
                </div>
            </div>
            <div class="col-lg-7">
                <x-section-heading :eyebrow="$promise->eyebrow" :heading="$promise->heading" :text="$promise->text" class="reveal" />
                <ol class="promise-steps">
                    @foreach ($promisePoints as [$label, $text, $icon])
                        <li class="reveal" data-delay="{{ $loop->index % 4 }}">
                            <span class="promise-ico"><i class="{{ $icon }}" aria-hidden="true"></i></span>
                            <div>
                                @if ($label)<span class="promise-label">{{ $label }}</span>@endif
                                <p>{{ $text }}</p>
                            </div>
                        </li>
                    @endforeach
                </ol>
            </div>
        </div>
    </div>
</section>
@endif

<x-delivery-band />
@endsection
