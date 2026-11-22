@extends('layouts.app')

@php
    $hero = cms('projects.hero');
    $intro = cms('projects.intro');
    $list = cms('projects.projects');
    $cap = cms('projects.capability');
    $projects = $list->items('projects');
    $categories = collect($projects)->pluck('category')->filter()->unique()->values();
@endphp

@section('title', plain_text($hero->get('title', 'Projects')))
@section('meta_description', (string) ($hero->subtitle))
@section('canonical', route('projects'))

@section('content')
<x-page-hero :section="$hero" />

@if ($intro->active())
<section class="section-sm" id="sec-intro">
    <div class="container-wide">
        <x-section-heading :eyebrow="$intro->eyebrow" :heading="$intro->heading" :text="$intro->text" :center="true" class="mb-0" />
    </div>
</section>
@endif

@if ($list->active() && $projects)
<section class="section pt-0" id="sec-projects">
    <div class="container-wide">
        @if ($categories->count() > 1)
            <div class="filter-bar" role="group" aria-label="Filter projects">
                <button type="button" class="active" data-filter="*">All Projects</button>
                @foreach ($categories as $cat)
                    <button type="button" data-filter="{{ \Illuminate\Support\Str::slug($cat) }}">{{ $cat }}</button>
                @endforeach
            </div>
        @endif

        <div class="project-list">
            @foreach ($projects as $p)
                <article @class(['project-row reveal', 'is-reverse' => $loop->even]) id="project-{{ $loop->iteration }}" data-cat="{{ \Illuminate\Support\Str::slug($p['category'] ?? '') }}">
                    <div class="project-media">
                        <x-media :src="$p['image'] ?? null" :alt="$p['title'] ?? ''" icon="fa-solid fa-helmet-safety" class="ratio-4x3 rounded-media" />
                        @if (! empty($p['image_2']))
                            <x-media :src="$p['image_2']" :alt="($p['title'] ?? '').' – detail'" class="project-media-2" />
                        @endif
                        <span class="project-index">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                    </div>
                    <div class="project-info">
                        @if (! empty($p['category']))<span class="tag">{{ $p['category'] }}</span>@endif
                        <h2>{{ $p['title'] ?? '' }}</h2>
                        <div class="prose">{{ rich_text($p['summary'] ?? '') }}</div>
                        @if ($specs = cms_pairs($p['specs'] ?? ''))
                            <dl class="spec-list is-compact">
                                @foreach ($specs as [$label, $value])
                                    <div><dt>{{ $label }}</dt><dd>{{ $value }}</dd></div>
                                @endforeach
                            </dl>
                        @endif
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>
@endif

@if ($cap->active())
<section class="cta-band is-large" id="sec-capability" @if ($cap->image) style="--cta-img:url('{{ asset($cap->image) }}')" @endif>
    <div class="container-wide">
        <div class="cta-inner reveal">
            <div>
                <span class="eyebrow is-gold">{{ $cap->eyebrow }}</span>
                <h2>{{ heading_text($cap->heading) }}</h2>
                <p>{{ $cap->text }}</p>
            </div>
            <a href="{{ route('contact') }}#enquiry" class="btn btn-gold btn-lg">{{ $cap->get('button_text', 'Discuss Your Project') }}<i class="fa-solid fa-arrow-right ms-2"></i></a>
        </div>
    </div>
</section>
@endif
@endsection

@push('scripts')
<script>
    document.querySelectorAll('.filter-bar [data-filter]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var f = btn.getAttribute('data-filter');
            document.querySelectorAll('.filter-bar [data-filter]').forEach(function (b) { b.classList.toggle('active', b === btn); });
            document.querySelectorAll('.project-row').forEach(function (row) {
                row.classList.toggle('d-none', f !== '*' && row.getAttribute('data-cat') !== f);
                row.classList.add('is-visible');
            });
        });
    });
</script>
@endpush
