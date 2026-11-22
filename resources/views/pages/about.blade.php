@extends('layouts.app')

@php
    $hero = cms('about.hero');
    $profile = cms('about.profile');
    $assoc = cms('about.association');
    $values = cms('about.values');
    $md = cms('about.md_message');
    $timeline = cms('about.timeline');
    $group = cms('about.group');
@endphp

@section('title', plain_text($hero->get('title', 'About Us')))
@section('meta_description', (string) (setting('seo_about_description') ?: $hero->subtitle))
@section('canonical', route('about'))

@section('content')
<x-page-hero :section="$hero" />

{{-- ================= PROFILE ================= --}}
@if ($profile->active())
<section class="section" id="sec-profile">
    <div class="container-wide">
        <div class="row g-5">
            <div class="col-lg-7 reveal reveal-left">
                <x-section-heading :eyebrow="$profile->eyebrow" :heading="$profile->heading" />
                @if ($profile->lead)<p class="lead-text">{{ $profile->lead }}</p>@endif
                <div class="prose">{{ rich_text($profile->text) }}</div>
            </div>
            <div class="col-lg-5 reveal reveal-right">
                <div class="stack-media">
                    <x-media :src="$profile->image" alt="Wear Plates Synergy plant" icon="fa-solid fa-industry" class="ratio-4x3 rounded-media" />
                    <x-media :src="$profile->image_2" alt="Wear plate products" icon="fa-solid fa-layer-group" class="ratio-16x9 rounded-media" />
                </div>
            </div>
        </div>
        @if ($points = $profile->lines('points'))
            <div class="bring-panel mt-5 reveal">
                <h3 class="bring-title">What Wear Plates Brings</h3>
                <ol class="bring-list">
                    @foreach ($points as $point)
                        <li><span>{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span><p>{{ $point }}</p></li>
                    @endforeach
                </ol>
            </div>
        @endif
    </div>
</section>
@endif

{{-- ================= MISSION & VALUES ================= --}}
@if ($values->active())
<section class="section values-band" id="sec-values">
    <div class="container-wide">
        <x-section-heading :eyebrow="$values->eyebrow" :heading="$values->heading" :text="$values->text" :center="true" :light="true" class="mb-5" />
        @php $vals = $values->items('values'); @endphp
        <div class="compass-layout">
            @foreach (array_slice($vals, 0, 2) as $v)
                <div class="value-card reveal {{ $loop->first ? 'pos-n' : 'pos-e' }}">
                    <span class="value-dir">{{ $v['direction'] ?? '' }}</span>
                    <i class="{{ $v['icon'] ?? 'fa-solid fa-star' }}" aria-hidden="true"></i>
                    <h3>{{ $v['title'] ?? '' }}</h3>
                    <p>{{ $v['text'] ?? '' }}</p>
                </div>
            @endforeach
            <div class="compass-center reveal reveal-zoom">
                @if ($values->image)
                    <img src="{{ asset($values->image) }}" alt="Compass – our values roadmap" loading="lazy">
                @else
                    <svg viewBox="0 0 200 200" class="compass-svg" aria-hidden="true">
                        <circle cx="100" cy="100" r="96" class="c-ring"/>
                        <circle cx="100" cy="100" r="78" class="c-ring2"/>
                        @for ($t = 0; $t < 72; $t++)
                            @php $a = deg2rad($t * 5); $r1 = $t % 9 === 0 ? 84 : 89; @endphp
                            <line x1="{{ 100 + 93 * cos($a) }}" y1="{{ 100 + 93 * sin($a) }}" x2="{{ 100 + $r1 * cos($a) }}" y2="{{ 100 + $r1 * sin($a) }}" class="c-tick"/>
                        @endfor
                        <polygon points="100,24 110,100 100,176 90,100" class="c-needle-v"/>
                        <polygon points="24,100 100,90 176,100 100,110" class="c-needle-h"/>
                        <polygon points="100,24 110,100 90,100" class="c-north"/>
                        <circle cx="100" cy="100" r="8" class="c-hub"/>
                        <text x="100" y="16" class="c-lbl">N</text><text x="188" y="104" class="c-lbl">E</text>
                        <text x="100" y="196" class="c-lbl">S</text><text x="12" y="104" class="c-lbl">W</text>
                    </svg>
                @endif
            </div>
            @foreach (array_slice($vals, 2, 2) as $v)
                <div class="value-card reveal {{ $loop->first ? 'pos-s' : 'pos-w' }}">
                    <span class="value-dir">{{ $v['direction'] ?? '' }}</span>
                    <i class="{{ $v['icon'] ?? 'fa-solid fa-star' }}" aria-hidden="true"></i>
                    <h3>{{ $v['title'] ?? '' }}</h3>
                    <p>{{ $v['text'] ?? '' }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ================= MD MESSAGE ================= --}}
@if ($md->active())
<section class="section" id="sec-md_message">
    <div class="container-wide">
        <div class="md-card reveal">
            <div class="md-photo">
                <x-media :src="$md->photo" :alt="$md->name" icon="fa-solid fa-user-tie" class="ratio-4x5" />
                <div class="md-name">
                    <strong>{{ $md->name }}</strong>
                    <span>{{ $md->designation }}</span>
                </div>
            </div>
            <div class="md-text">
                <x-section-heading :eyebrow="$md->eyebrow" :heading="$md->heading" />
                <i class="fa-solid fa-quote-left md-quote-mark" aria-hidden="true"></i>
                <div class="prose">{{ rich_text($md->message) }}</div>
                @if ($md->quote)<p class="md-sign">“{{ $md->quote }}”</p>@endif
                <a href="{{ route('leadership') }}" class="btn btn-link-arrow mt-2">Meet our leadership team <i class="fa-solid fa-arrow-right"></i></a>
            </div>
        </div>
    </div>
</section>
@endif

{{-- ================= ASSOCIATIONS ================= --}}
@if ($assoc->active() && $assoc->items('partners'))
<section class="section bg-soft" id="sec-association">
    <div class="container-wide">
        <x-section-heading :eyebrow="$assoc->eyebrow" :heading="$assoc->heading" :text="$assoc->text" :center="true" class="mb-5" />
        <div class="row g-4 justify-content-center">
            @foreach ($assoc->items('partners') as $partner)
                <div class="col-md-6 col-lg-4 reveal" data-delay="{{ $loop->index }}">
                    <div class="partner-card">
                        <div class="partner-logo">
                            @if (! empty($partner['logo']))<img src="{{ asset($partner['logo']) }}" alt="{{ $partner['name'] ?? '' }}" loading="lazy">@else<i class="fa-solid fa-handshake" aria-hidden="true"></i>@endif
                        </div>
                        <span class="tag">{{ $partner['role'] ?? '' }}</span>
                        <h3>{{ $partner['name'] ?? '' }}</h3>
                        <p>{{ $partner['text'] ?? '' }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ================= TIMELINE ================= --}}
@if ($timeline->active() && $timeline->items('items'))
<section class="section" id="sec-timeline">
    <div class="container-wide">
        <x-section-heading :eyebrow="$timeline->eyebrow" :heading="$timeline->heading" :center="true" class="mb-5" />
        <ol class="timeline">
            @foreach ($timeline->items('items') as $item)
                <li class="reveal" data-delay="{{ $loop->index % 3 }}">
                    <span class="tl-year">{{ $item['year'] ?? '' }}</span>
                    <div class="tl-card">
                        <h3>{{ $item['title'] ?? '' }}</h3>
                        <p>{{ $item['text'] ?? '' }}</p>
                    </div>
                </li>
            @endforeach
        </ol>
    </div>
</section>
@endif

{{-- ================= SYNERGY GROUP ================= --}}
@if ($group->active())
<section class="section bg-dark-steel" id="sec-group">
    <div class="container-wide">
        <div class="row g-5 align-items-start">
            <div class="col-lg-5">
                <x-section-heading :eyebrow="$group->eyebrow" :heading="$group->heading" :text="$group->text" :light="true" />
                @if ($group->lines('statements'))
                    <ul class="statement-list">
                        @foreach ($group->lines('statements') as $s)<li>{{ $s }}</li>@endforeach
                    </ul>
                @endif
                @if ($group->website)
                    <a href="{{ cms_link(str_starts_with($group->website, 'http') ? $group->website : 'https://'.$group->website) }}" target="_blank" rel="noopener" class="btn btn-outline-light mt-3"><i class="fa-solid fa-globe me-2"></i>{{ $group->website }}</a>
                @endif
            </div>
            <div class="col-lg-7">
                <div class="row g-3 mb-4">
                    <div class="col-sm-6"><x-media :src="$group->image" alt="Synergy infrastructure" icon="fa-solid fa-building" class="ratio-4x3 rounded-media" /><p class="caption-light">Synergy Infrastructure</p></div>
                    <div class="col-sm-6"><x-media :src="$group->image_2" alt="Synergy workshop" icon="fa-solid fa-warehouse" class="ratio-4x3 rounded-media" /><p class="caption-light">Synergy Workshop</p></div>
                </div>
                @if ($group->items('products'))
                    <div class="tile-grid">
                        @foreach ($group->items('products') as $p)
                            <figure class="mini-tile">
                                <x-media :src="$p['image'] ?? null" :alt="$p['name'] ?? ''" icon="fa-solid fa-cube" class="ratio-1x1" />
                                <figcaption>{{ $p['name'] ?? '' }}</figcaption>
                            </figure>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
@endif
@endsection
