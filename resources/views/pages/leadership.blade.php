@extends('layouts.app')

@php
    $hero = cms('leadership.hero');
    $md = cms('leadership.md_message');
    $intro = cms('leadership.intro');
    $leaders = cms('leadership.leaders');
    $team = cms('leadership.team');
@endphp

@section('title', plain_text($hero->get('title', 'Leadership')))
@section('meta_description', (string) ($hero->subtitle))
@section('canonical', route('leadership'))

@section('content')
<x-page-hero :section="$hero" :crumbs="['Company' => route('about')]" />

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
                <a href="#sec-leaders" class="btn btn-link-arrow mt-2">Meet our leadership team <i class="fa-solid fa-arrow-right"></i></a>
            </div>
        </div>
    </div>
</section>
@endif

{{-- ================= LEADERS ================= --}}
@if ($intro->active())
<section class="section-sm bg-soft" id="sec-intro">
    <div class="container-wide">
        <x-section-heading :eyebrow="$intro->eyebrow" :heading="$intro->heading" :text="$intro->text" :center="true" class="mb-0" />
    </div>
</section>
@endif

@if ($leaders->active())
<section @class(['section bg-soft', 'pt-0' => $intro->active()]) id="sec-leaders">
    <div class="container-wide">
        @foreach ($leaders->items('leaders') as $leader)
            <article @class(['leader-profile reveal', 'is-reverse' => $loop->even])>
                <div class="leader-photo">
                    <x-media :src="$leader['photo'] ?? null" :alt="$leader['name'] ?? ''" icon="fa-solid fa-user-tie" class="ratio-4x5" />
                    @if (! empty($leader['quote']))
                        <blockquote class="leader-quote"><i class="fa-solid fa-quote-left"></i>{{ $leader['quote'] }}</blockquote>
                    @endif
                </div>
                <div class="leader-body">
                    <span class="eyebrow">{{ $leader['designation'] ?? '' }}@if (! empty($leader['location'])) · {{ $leader['location'] }}@endif</span>
                    <h2 class="leader-name">{{ $leader['name'] ?? '' }}</h2>
                    <div class="prose">{{ rich_text($leader['summary'] ?? '') }}</div>
                    @if ($hl = cms_lines($leader['highlights'] ?? ''))
                        <h3 class="sub-title-sm">Profile</h3>
                        <ul class="highlight-list">
                            @foreach ($hl as $h)<li>{{ $h }}</li>@endforeach
                        </ul>
                    @endif
                </div>
            </article>
        @endforeach
    </div>
</section>
@endif

@if ($team->active() && $team->items('members'))
<section class="section" id="sec-team">
    <div class="container-wide">
        <x-section-heading :eyebrow="$team->eyebrow" :heading="$team->heading" :text="$team->text" :center="true" class="mb-5" />
        <div class="row g-4">
            @foreach ($team->items('members') as $m)
                @php $initials = collect(preg_split('/\s+/', trim($m['name'] ?? '')))->filter()->map(fn ($w) => mb_substr($w, 0, 1))->take(2)->implode(''); @endphp
                <div class="col-md-6 col-xl-4 reveal" data-delay="{{ $loop->index % 3 }}">
                    <div class="member-card">
                        <div class="member-head">
                            @if (! empty($m['photo']))
                                <img src="{{ asset($m['photo']) }}" alt="{{ $m['name'] ?? '' }}" class="member-avatar" loading="lazy">
                            @else
                                <span class="member-avatar is-initials">{{ $initials }}</span>
                            @endif
                            <div>
                                <h3>{{ $m['name'] ?? '' }}</h3>
                                <span>{{ $m['designation'] ?? '' }}</span>
                            </div>
                            @if (! empty($m['years']))<span class="member-years"><strong>{{ $m['years'] }}+</strong>yrs</span>@endif
                        </div>
                        <p>{{ $m['experience'] ?? '' }}</p>
                    </div>
                </div>
            @endforeach
        </div>
        @if ($team->promoters)
            <p class="promoter-note"><i class="fa-solid fa-handshake me-2"></i>{{ $team->promoters }}</p>
        @endif
    </div>
</section>
@endif
@endsection
