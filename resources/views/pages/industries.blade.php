@extends('layouts.app')

@php
    $hero = cms('industries.hero');
    $intro = cms('industries.intro');
    $sectors = cms('industries.sectors')->items('sectors');
    $sectorsOn = cms('industries.sectors')->active();
    $rolls = cms('industries.rolls');
    $cap = cms('industries.capability');
@endphp

@section('title', plain_text($hero->get('title', 'Industries & Services')))
@section('meta_description', (string) ($hero->subtitle))
@section('canonical', route('industries'))

@section('content')
<x-page-hero :section="$hero" />

@if ($intro->active())
<section class="section-sm" id="sec-intro">
    <div class="container-wide">
        <x-section-heading :eyebrow="$intro->eyebrow" :heading="$intro->heading" :text="$intro->text" :center="true" class="mb-0" />
    </div>
</section>
@endif

@if ($sectorsOn && $sectors)
<section class="section pt-0" id="sec-sectors">
    <div class="container-wide">
        <div class="sector-layout">
            <div class="sector-tabs" role="tablist" aria-label="Industry sectors">
                @foreach ($sectors as $s)
                    @php $sid = 'sector-'.\Illuminate\Support\Str::slug($s['name'] ?? 'sector-'.$loop->index); @endphp
                    <button type="button" role="tab" id="{{ $sid }}-tab" data-bs-toggle="tab" data-bs-target="#{{ $sid }}" aria-controls="{{ $sid }}"
                            aria-selected="{{ $loop->first ? 'true' : 'false' }}" @class(['sector-tab', 'active' => $loop->first])>
                        <i class="{{ $s['icon'] ?? 'fa-solid fa-industry' }}" aria-hidden="true"></i>
                        <span>{{ $s['name'] ?? '' }}</span>
                        <em>{{ count(cms_lines($s['jobs'] ?? '')) }}</em>
                    </button>
                @endforeach
            </div>
            <div class="tab-content sector-panels">
                @foreach ($sectors as $s)
                    @php $sid = 'sector-'.\Illuminate\Support\Str::slug($s['name'] ?? 'sector-'.$loop->index); @endphp
                    <div @class(['tab-pane fade sector-panel', 'show active' => $loop->first]) id="{{ $sid }}" role="tabpanel" aria-labelledby="{{ $sid }}-tab" tabindex="0">
                        <x-media :src="$s['image'] ?? null" :alt="$s['name'] ?? ''" :icon="$s['icon'] ?? 'fa-solid fa-industry'" class="ratio-21x9 rounded-media" />
                        <div class="sector-head">
                            <span class="sector-ico"><i class="{{ $s['icon'] ?? 'fa-solid fa-industry' }}"></i></span>
                            <div>
                                <h2>{{ $s['name'] ?? '' }}</h2>
                                <p>{{ $s['summary'] ?? '' }}</p>
                            </div>
                        </div>
                        <h3 class="sub-title-sm">Key jobs &amp; services</h3>
                        <ul class="job-list">
                            @foreach (cms_lines($s['jobs'] ?? '') as $job)<li>{{ $job }}</li>@endforeach
                        </ul>
                        <a href="{{ route('contact') }}#enquiry" class="btn btn-brand mt-3">Discuss a {{ $s['name'] ?? '' }} requirement<i class="fa-solid fa-arrow-right ms-2"></i></a>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
@endif

@if ($cap->active())
<section class="section bg-soft" id="sec-capability">
    <div class="container-wide">
        <div class="row g-5 align-items-center">
            <div class="col-lg-6 reveal reveal-left">
                <x-section-heading :eyebrow="$cap->eyebrow" :heading="$cap->heading" :text="$cap->text" />
                <ul class="tick-list two-col">
                    @foreach ($cap->lines('points') as $p)<li>{{ $p }}</li>@endforeach
                </ul>
            </div>
            <div class="col-lg-6 reveal reveal-right">
                <x-media :src="$cap->image" alt="Rebuilding and cladding work" icon="fa-solid fa-screwdriver-wrench" class="ratio-4x3 rounded-media" />
            </div>
        </div>
    </div>
</section>
@endif

@if ($rolls->active() && count($rolls->table('table')) > 1)
<section class="section" id="sec-rolls">
    <div class="container-wide">
        <x-section-heading :eyebrow="$rolls->eyebrow" :heading="$rolls->heading" :text="$rolls->text" class="mb-4" />
        @include('partials.data-table', ['rows' => $rolls->table('table'), 'class' => 'reveal'])
        @if ($rolls->note)<p class="table-note"><i class="fa-solid fa-circle-info me-2"></i>{{ $rolls->note }}</p>@endif
    </div>
</section>
@endif
@endsection

@push('scripts')
<script>
    // Open the sector tab named in the URL hash (links from the home page).
    document.addEventListener('DOMContentLoaded', function () {
        if (!location.hash || !window.bootstrap) return;
        var btn = document.getElementById(location.hash.slice(1) + '-tab');
        if (btn) { bootstrap.Tab.getOrCreateInstance(btn).show(); document.getElementById('sec-sectors').scrollIntoView(); }
    });
</script>
@endpush
