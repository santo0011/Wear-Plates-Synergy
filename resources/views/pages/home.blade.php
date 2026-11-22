@extends('layouts.app')

@section('canonical', route('home'))

@php
    $hero = cms('home.hero');
    $trust = cms('home.trust');
    $about = cms('home.about');
    $stats = cms('home.stats');
    $prod = cms('home.products');
    $ve = cms('home.value_engineering');
    $ind = cms('home.industries');
    $process = cms('home.process');
    $proj = cms('home.projects');
    $clients = cms('home.clients');
    $cta = cms('home.cta');
    $sectors = cms('industries.sectors')->items('sectors');
    $projects = array_slice(cms('projects.projects')->items('projects'), 0, 3);
    $slides = $hero->items('slides');
@endphp

@section('content')

{{-- ================= HERO SLIDER ================= --}}
@if ($hero->active() && $slides)
<section class="hero" id="sec-hero" aria-label="Highlights">
    <div id="heroCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="6500" data-bs-pause="hover">
        <div class="carousel-inner">
            @foreach ($slides as $i => $slide)
                <div @class(['carousel-item', 'active' => $i === 0])>
                    <div @class(['hero-bg', 'has-image' => ! empty($slide['image'])]) @if (! empty($slide['image'])) style="background-image:url('{{ asset($slide['image']) }}')" @endif aria-hidden="true"></div>
                    <div class="hero-shade" aria-hidden="true"></div>
                    @if (empty($slide['image']))
                        <div class="hero-mark" aria-hidden="true"><img src="{{ asset(setting('logo') ?: 'images/brand/logo.png') }}" alt=""></div>
                    @endif
                    <div class="container-wide hero-content">
                        <div class="hero-copy">
                            @if (! empty($slide['eyebrow']))<span class="hero-eyebrow">{{ $slide['eyebrow'] }}</span>@endif
                            @if ($i === 0)
                                <h1 class="hero-title">{{ heading_text($slide['title'] ?? '') }}</h1>
                            @else
                                <h2 class="hero-title">{{ heading_text($slide['title'] ?? '') }}</h2>
                            @endif
                            @if (! empty($slide['text']))<p class="hero-text">{{ $slide['text'] }}</p>@endif
                            <div class="hero-actions">
                                @if (! empty($slide['button_text']))
                                    <a href="{{ cms_link($slide['button_link'] ?? null, route('products.index')) }}" class="btn btn-gold btn-lg">{{ $slide['button_text'] }}<i class="fa-solid fa-arrow-right ms-2"></i></a>
                                @endif
                                <a href="{{ route('contact') }}#enquiry" class="btn btn-outline-light btn-lg">Get a Quote</a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        @if (count($slides) > 1)
            <div class="container-wide hero-controls">
                <div class="hero-dots">
                    @foreach ($slides as $i => $slide)
                        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="{{ $i }}" @class(['active' => $i === 0]) @if ($i === 0) aria-current="true" @endif aria-label="Slide {{ $i + 1 }}: {{ plain_text($slide['title'] ?? '') }}">
                            <span class="num">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>
                            <span class="lbl">{{ \Illuminate\Support\Str::limit(plain_text($slide['eyebrow'] ?? $slide['title'] ?? ''), 34) }}</span>
                            <span class="bar"><i></i></span>
                        </button>
                    @endforeach
                </div>
                <div class="hero-arrows">
                    <button type="button" data-bs-target="#heroCarousel" data-bs-slide="prev" aria-label="Previous slide"><i class="fa-solid fa-arrow-left"></i></button>
                    <button type="button" data-bs-target="#heroCarousel" data-bs-slide="next" aria-label="Next slide"><i class="fa-solid fa-arrow-right"></i></button>
                </div>
            </div>
        @endif
    </div>
</section>
@endif

{{-- ================= TRUST STRIP ================= --}}
@if ($trust->active() && $trust->items('items'))
<section class="trust-strip" id="sec-trust">
    <div class="container-wide">
        <ul class="trust-list">
            @foreach ($trust->items('items') as $item)
                <li class="reveal" data-delay="{{ min($loop->index, 5) }}">
                    <span class="trust-icon"><i class="{{ $item['icon'] ?? 'fa-solid fa-check' }}" aria-hidden="true"></i></span>
                    <span><strong>{{ $item['title'] ?? '' }}</strong><small>{{ $item['text'] ?? '' }}</small></span>
                </li>
            @endforeach
        </ul>
    </div>
</section>
@endif

{{-- ================= ABOUT ================= --}}
@if ($about->active())
<section class="section home-about" id="sec-about">
    <div class="container-wide">
        <div class="row align-items-start g-4 g-xl-5">
            <div class="col-lg-6 reveal reveal-left">
                <div class="about-collage">
                    <x-media :src="$about->image" :alt="plain_text($about->heading)" icon="fa-solid fa-industry" class="about-main" />
                    <x-media :src="$about->image_2" alt="Wear plates" icon="fa-solid fa-layer-group" class="about-sub" />
                    @if ($about->badge_value)
                        <div class="about-badge"><span>{{ $about->badge_label }}</span><strong>{{ $about->badge_value }}</strong></div>
                    @endif
                </div>
            </div>
            <div class="col-lg-6 reveal reveal-right">
                <x-section-heading :eyebrow="$about->eyebrow" :heading="$about->heading" />
                <div class="prose">{{ rich_text($about->text) }}</div>
            </div>
            @if ($about->lines('points'))
                <div class="col-12 home-about-points reveal">
                    <div class="tick-list two-col home-about-point-grid" role="list">
                        @foreach ($about->lines('points') as $point)
                            <div class="home-about-point" role="listitem">{{ $point }}</div>
                        @endforeach
                        <div class="d-flex flex-wrap align-items-center gap-3 about-actions">
                            <a href="{{ route('about') }}" class="btn btn-brand">{{ $about->get('button_text', 'More About Us') }}<i class="fa-solid fa-arrow-right ms-2"></i></a>
                            <a href="{{ route('leadership') }}" class="btn btn-link-arrow">Meet our leadership <i class="fa-solid fa-arrow-right"></i></a>
                        </div>
                    </div>
                </div>
            @else
                <div class="col-12 reveal">
                    <div class="d-flex flex-wrap gap-3 about-actions">
                        <a href="{{ route('about') }}" class="btn btn-brand">{{ $about->get('button_text', 'More About Us') }}<i class="fa-solid fa-arrow-right ms-2"></i></a>
                        <a href="{{ route('leadership') }}" class="btn btn-link-arrow">Meet our leadership <i class="fa-solid fa-arrow-right"></i></a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</section>
@endif

{{-- ================= STATS ================= --}}
@if ($stats->active() && $stats->items('items'))
<section class="stats-band" id="sec-stats">
    <div class="container-wide">
        <x-section-heading :eyebrow="$stats->eyebrow" :heading="$stats->heading" :light="true" :center="true" class="mb-5" />
        <div class="stats-grid">
            @foreach ($stats->items('items') as $item)
                @php $num = preg_replace('/\D/', '', (string) ($item['value'] ?? '')); @endphp
                <div class="stat-cell reveal" data-delay="{{ min($loop->index, 5) }}">
                    <i class="{{ $item['icon'] ?? 'fa-solid fa-chart-simple' }} stat-ico" aria-hidden="true"></i>
                    <div class="stat-num">
                        @if ($num !== '')<span data-count="{{ $num }}">{{ number_format((int) $num) }}</span>@else<span>{{ $item['value'] ?? '' }}</span>@endif<em>{{ $item['suffix'] ?? '' }}</em>
                    </div>
                    <div class="stat-lbl">{{ $item['label'] ?? '' }}</div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ================= PRODUCTS ================= --}}
@if ($prod->active() && $categories->isNotEmpty())
<section class="section bg-soft" id="sec-products">
    <div class="container-wide">
        <div class="sec-head-row">
            <x-section-heading :eyebrow="$prod->eyebrow" :heading="$prod->heading" :text="$prod->text" />
            <a href="{{ route('products.index') }}" class="btn btn-outline-brand">View All Products<i class="fa-solid fa-arrow-right ms-2"></i></a>
        </div>
        <div class="row g-4">
            @foreach ($categories as $category)
                <div class="col-sm-6 col-xl-3 reveal" data-delay="{{ $loop->index % 4 }}">
                    @include('products.partials.category-card', ['category' => $category])
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ================= VALUE ENGINEERING ================= --}}
@if ($ve->active())
<section class="ve-band" id="sec-value_engineering" @if ($ve->image) style="--ve-img:url('{{ asset($ve->image) }}')" @endif>
    <div class="container-wide position-relative">
        <div class="row g-5 align-items-center">
            <div class="col-lg-5 reveal reveal-left">
                <x-section-heading :eyebrow="$ve->eyebrow" :heading="$ve->heading" :light="true" />
                <div class="prose is-light">{{ rich_text($ve->text) }}</div>
                <div class="lpf-equation">
                    <span>Right Material</span><i class="fa-solid fa-plus"></i><span>Right Place</span><i class="fa-solid fa-equals"></i><strong>Higher LPF &amp; ROI</strong>
                </div>
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

{{-- ================= INDUSTRIES ================= --}}
@if ($ind->active() && $sectors)
<section class="section" id="sec-industries">
    <div class="container-wide">
        <div class="sec-head-row">
            <x-section-heading :eyebrow="$ind->eyebrow" :heading="$ind->heading" :text="$ind->text" />
            <a href="{{ route('industries') }}" class="btn btn-outline-brand">Industries &amp; Services<i class="fa-solid fa-arrow-right ms-2"></i></a>
        </div>
        <div class="industry-grid">
            @foreach ($sectors as $sector)
                <a href="{{ route('industries') }}#sector-{{ \Illuminate\Support\Str::slug($sector['name'] ?? '') }}" class="industry-tile reveal" data-delay="{{ $loop->index % 4 }}">
                    <x-media :src="$sector['image'] ?? null" :alt="$sector['name'] ?? ''" :icon="$sector['icon'] ?? 'fa-solid fa-industry'" class="industry-img" />
                    <span class="industry-body">
                        <i class="{{ $sector['icon'] ?? 'fa-solid fa-industry' }}" aria-hidden="true"></i>
                        <strong>{{ $sector['name'] ?? '' }}</strong>
                        <small>{{ count(cms_lines($sector['jobs'] ?? '')) }} key services</small>
                    </span>
                </a>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ================= PROCESS ================= --}}
@if ($process->active())
<section class="section bg-soft" id="sec-process">
    <div class="container-wide">
        <div class="row g-5 align-items-center">
            <div class="col-lg-5 reveal reveal-left">
                <x-section-heading :eyebrow="$process->eyebrow" :heading="$process->heading" :text="$process->text" />
                <x-media :src="$process->image" alt="Robotic weld cladding" icon="fa-solid fa-robot" class="ratio-4x3 rounded-media mt-4" />
            </div>
            <div class="col-lg-7">
                <ol class="process-list">
                    @foreach ($process->items('steps') as $step)
                        <li class="reveal" data-delay="{{ $loop->index }}">
                            <span class="process-ico"><i class="{{ $step['icon'] ?? 'fa-solid fa-check' }}" aria-hidden="true"></i></span>
                            <div>
                                <span class="process-step">Step {{ $loop->iteration }}</span>
                                <h3>{{ $step['title'] ?? '' }}</h3>
                                <p>{{ $step['text'] ?? '' }}</p>
                            </div>
                        </li>
                    @endforeach
                </ol>
                <a href="{{ route('infrastructure') }}" class="btn btn-link-arrow mt-2">Explore our infrastructure <i class="fa-solid fa-arrow-right"></i></a>
            </div>
        </div>
    </div>
</section>
@endif

{{-- ================= PROJECTS ================= --}}
@if ($proj->active() && $projects)
<section class="section" id="sec-projects">
    <div class="container-wide">
        <div class="sec-head-row">
            <x-section-heading :eyebrow="$proj->eyebrow" :heading="$proj->heading" :text="$proj->text" />
            <a href="{{ route('projects') }}" class="btn btn-outline-brand">All Projects<i class="fa-solid fa-arrow-right ms-2"></i></a>
        </div>
        <div class="row g-4">
            @foreach ($projects as $project)
                <div class="col-md-6 col-lg-4 reveal" data-delay="{{ $loop->index }}">
                    <a href="{{ route('projects') }}#project-{{ $loop->iteration }}" class="project-card">
                        <x-media :src="$project['image'] ?? null" :alt="$project['title'] ?? ''" icon="fa-solid fa-helmet-safety" class="project-img" />
                        <span class="project-body">
                            @if (! empty($project['category']))<span class="tag">{{ $project['category'] }}</span>@endif
                            <strong>{{ $project['title'] ?? '' }}</strong>
                            <small>{{ \Illuminate\Support\Str::limit($project['summary'] ?? '', 120) }}</small>
                            <span class="more">View project <i class="fa-solid fa-arrow-right"></i></span>
                        </span>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ================= CLIENTS ================= --}}
@if ($clients->active() && $clients->items('clients'))
<section class="section-sm clients-band" id="sec-clients">
    <div class="container-wide">
        <x-section-heading :eyebrow="$clients->eyebrow" :heading="$clients->heading" :text="$clients->text" :center="true" class="mb-5" />
    </div>
    <div class="marquee" aria-label="Our clients">
        <div class="marquee-track">
            @foreach ([1, 2] as $pass)
                @foreach ($clients->items('clients') as $client)
                    <span class="client-logo" @if ($pass === 2) aria-hidden="true" @endif>
                        @if (! empty($client['logo']))
                            <img src="{{ asset($client['logo']) }}" alt="{{ $pass === 1 ? ($client['name'] ?? '') : '' }}" loading="lazy">
                        @else
                            {{ $client['name'] ?? '' }}
                        @endif
                    </span>
                @endforeach
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ================= PRESENCE ================= --}}
@if ($locations->isNotEmpty())
<section class="section bg-soft" id="sec-presence">
    <div class="container-wide">
        <div class="row g-5 align-items-center">
            <div class="col-lg-5 reveal reveal-left">
                <x-section-heading eyebrow="Our Presence" heading="Wherever You Are, **We Are**"
                    text="Corporate office in Mumbai, a manufacturing plant at GIDC Ankleshwar and regional offices across India – with fast delivery by air and surface through Blue Dart and DHL." />
                <div class="presence-counts">
                    <div><strong>{{ $locations->where('type', 'corporate')->count() }}</strong><span>Corporate Office</span></div>
                    <div><strong>{{ $locations->where('type', 'plant')->count() }}</strong><span>Manufacturing Plant</span></div>
                    <div><strong>{{ $locations->where('type', 'regional')->count() }}</strong><span>Regional Offices</span></div>
                </div>
                <a href="{{ route('presence') }}" class="btn btn-brand mt-4">View All Locations<i class="fa-solid fa-arrow-right ms-2"></i></a>
            </div>
            <div class="col-lg-7 reveal reveal-right">
                <x-india-map :locations="$locations" />
            </div>
        </div>
    </div>
</section>
@endif

{{-- ================= CTA ================= --}}
@if ($cta->active())
<section class="cta-band" id="sec-cta" @if ($cta->image) style="--cta-img:url('{{ asset($cta->image) }}')" @endif>
    <div class="container-wide">
        <div class="cta-inner reveal">
            <div>
                <h2>{{ heading_text($cta->heading) }}</h2>
                <p>{{ $cta->text }}</p>
            </div>
            <a href="{{ cms_link($cta->button_link, route('contact')) }}" class="btn btn-gold btn-lg">{{ $cta->get('button_text', 'Request a Quote') }}<i class="fa-solid fa-arrow-right ms-2"></i></a>
        </div>
    </div>
</section>
@endif
@endsection
