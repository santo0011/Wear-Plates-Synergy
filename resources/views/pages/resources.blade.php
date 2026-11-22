@extends('layouts.app')

@php
    $hero = cms('resources.hero');
    $hf = cms('resources.hardfacing');
    $wear = cms('resources.wear');
    $mining = cms('resources.mining');
    $world = cms('resources.world');
    $apps = cms('resources.applications');
@endphp

@section('title', plain_text($hero->get('title', 'Technical Resources')))
@section('meta_description', (string) ($hero->subtitle))
@section('canonical', route('resources'))

@section('content')
<x-page-hero :section="$hero" :crumbs="['Capabilities' => route('infrastructure')]" />

<nav class="cat-jump" aria-label="On this page">
    <div class="container-wide">
        <div class="cat-jump-inner">
            @if ($hf->active())<a href="#sec-hardfacing"><i class="fa-solid fa-fire-flame-curved"></i>Hardfacing</a>@endif
            @if ($wear->active())<a href="#sec-wear"><i class="fa-solid fa-burst"></i>Types of Wear</a>@endif
            @if ($mining->active())<a href="#sec-mining"><i class="fa-solid fa-truck-monster"></i>Earth Moving &amp; Mining</a>@endif
            @if ($world->active())<a href="#sec-world"><i class="fa-solid fa-layer-group"></i>World of Wear Plates</a>@endif
            @if ($products->isNotEmpty())<a href="#sec-datasheets"><i class="fa-solid fa-table"></i>Data Sheets</a>@endif
            @if ($apps->active())<a href="#sec-applications"><i class="fa-solid fa-check-double"></i>Applications</a>@endif
        </div>
    </div>
</nav>

@if ($hf->active())
<section class="section" id="sec-hardfacing">
    <div class="container-wide">
        <div class="row g-5">
            <div class="col-lg-7">
                <x-section-heading :eyebrow="$hf->eyebrow" :heading="$hf->heading" />
                @if ($hf->lead)<p class="lead-text">{{ $hf->lead }}</p>@endif
                <div class="prose article">{{ rich_text($hf->text) }}</div>
            </div>
            <div class="col-lg-5">
                <div class="sticky-aside">
                    <x-media :src="$hf->image" alt="Hardfacing" icon="fa-solid fa-fire-flame-curved" class="ratio-4x3 rounded-media mb-4" />
                    <div class="fact-grid">
                        @foreach ($hf->items('facts') as $f)
                            <div class="fact-card reveal" data-delay="{{ $loop->index }}">
                                <strong>{{ $f['value'] ?? '' }}</strong>
                                <span>{{ $f['label'] ?? '' }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endif

@if ($wear->active() && $wear->items('types'))
<section class="section bg-soft" id="sec-wear">
    <div class="container-wide">
        <x-section-heading :eyebrow="$wear->eyebrow" :heading="$wear->heading" :text="$wear->text" :center="true" class="mb-5" />
        <div class="feature-grid is-3">
            @foreach ($wear->items('types') as $t)
                <div class="feature-card reveal" data-delay="{{ $loop->index % 3 }}">
                    <span class="feature-ico"><i class="{{ $t['icon'] ?? 'fa-solid fa-burst' }}" aria-hidden="true"></i></span>
                    <h3>{{ $t['title'] ?? '' }}</h3>
                    <p>{{ $t['text'] ?? '' }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

@if ($mining->active())
<section class="section" id="sec-mining">
    <div class="container-wide">
        <div class="row g-5 align-items-center">
            <div class="col-lg-6 reveal reveal-left">
                <x-media :src="$mining->image" alt="Earth moving and mining" icon="fa-solid fa-truck-monster" class="ratio-4x3 rounded-media" />
            </div>
            <div class="col-lg-6 reveal reveal-right">
                <x-section-heading :eyebrow="$mining->eyebrow" :heading="$mining->heading" />
                <div class="prose">{{ rich_text($mining->text) }}</div>
            </div>
        </div>
    </div>
</section>
@endif

@if ($world->active() && $world->items('tiers'))
<section class="section bg-dark-steel" id="sec-world">
    <div class="container-wide">
        <x-section-heading :eyebrow="$world->eyebrow" :heading="$world->heading" :text="$world->text" :center="true" :light="true" class="mb-5" />
        @php $tiers = $world->items('tiers'); $n = count($tiers); @endphp
        <div class="pyramid">
            <div class="pyramid-top reveal"><i class="fa-solid fa-globe"></i>World of Wear Plates</div>
            @foreach ($tiers as $tier)
                <div @class(['pyramid-tier reveal', 'no-name' => empty($tier['name'])]) style="--w: {{ 62 + (38 * ($loop->iteration / max($n, 1))) }}%; --t: {{ $loop->index }}" data-delay="{{ min($loop->index, 5) }}">
                    @if (! empty($tier['name']))<span class="tier-name">{{ $tier['name'] }}</span>@endif
                    <div class="tier-items">
                        @foreach (cms_lines($tier['items'] ?? '') as $it)<span>{{ $it }}</span>@endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

@if ($products->isNotEmpty())
<section class="section" id="sec-datasheets">
    <div class="container-wide">
        <x-section-heading eyebrow="Technical Data" heading="Product **Data Sheets**" text="Bead properties, weld chemistry and technical data for our registered WPI grades. Select a grade to view its data." class="mb-4" />
        <div class="ds-layout">
            <div class="nav ds-tabs" role="tablist" aria-label="Data sheets">
                @foreach ($products as $p)
                    <button type="button" @class(['ds-tab', 'active' => $loop->first]) data-bs-toggle="tab" data-bs-target="#ds-{{ $p->id }}" role="tab" aria-controls="ds-{{ $p->id }}" aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                        <strong>{{ $p->name }}</strong><small>{{ $p->category?->name }}</small>
                    </button>
                @endforeach
            </div>
            <div class="tab-content">
                @foreach ($products as $p)
                    <div @class(['tab-pane fade', 'show active' => $loop->first]) id="ds-{{ $p->id }}" role="tabpanel" tabindex="0">
                        <div class="ds-head">
                            <div>
                                <h3>{{ $p->name }}</h3>
                                @if ($p->subtitle)<p>{{ $p->subtitle }}</p>@endif
                            </div>
                            <a href="{{ $p->url }}" class="btn btn-outline-brand btn-sm">Product page<i class="fa-solid fa-arrow-right ms-2"></i></a>
                        </div>
                        <h4 class="sub-title-sm">{{ $p->data_table_title ?: 'Technical Data' }}</h4>
                        @include('partials.data-table', ['rows' => cms_table($p->data_table)])
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
@endif

@if ($apps->active() && $apps->items('items'))
<section class="section bg-soft" id="sec-applications">
    <div class="container-wide">
        <x-section-heading :eyebrow="$apps->eyebrow" :heading="$apps->heading" :text="$apps->text" :center="true" class="mb-5" />
        <div class="gallery-grid is-4">
            @foreach ($apps->items('items') as $a)
                <figure class="gallery-item reveal" data-delay="{{ $loop->index % 4 }}">
                    <x-media :src="$a['image'] ?? null" :alt="$a['title'] ?? ''" icon="fa-solid fa-gears" class="ratio-4x3" />
                    <figcaption>{{ $a['title'] ?? '' }}</figcaption>
                </figure>
            @endforeach
        </div>
    </div>
</section>
@endif
@endsection
