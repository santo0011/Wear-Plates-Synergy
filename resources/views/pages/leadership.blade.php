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

{{-- ================= INTRO ================= --}}
@if ($intro->active())
<section class="section-sm" id="sec-intro">
    <div class="container-wide">
        <x-section-heading :eyebrow="$intro->eyebrow" :heading="$intro->heading" :text="$intro->text" :center="true" class="mb-0" />
    </div>
</section>
@endif

{{-- ================= LEADERS (MD message first, then leader profiles – one pattern) ================= --}}
@php
    $people = [];
    if ($md->active() && $md->name) {
        $people[] = [
            'id' => 'sec-md_message',
            'photo' => $md->photo,
            'name' => $md->name,
            'designation' => $md->designation,
            'location' => null,
            'title' => plain_text($md->heading),
            'summary' => $md->message,
            'highlights' => '',
            'quote' => $md->quote,
        ];
    }
    if ($leaders->active()) {
        foreach ($leaders->items('leaders') as $leader) {
            $people[] = $leader + ['id' => null, 'title' => null];
        }
    }
@endphp
@if ($people)
<section @class(['section', 'pt-0' => $intro->active()]) id="sec-leaders">
    <div class="container-wide">
        @foreach ($people as $person)
            <article @class(['leader-profile reveal', 'is-reverse' => $loop->even]) @if ($person['id']) id="{{ $person['id'] }}" @endif>
                <div class="leader-photo">
                    <x-media :src="$person['photo'] ?? null" :alt="$person['name'] ?? ''" icon="fa-solid fa-user-tie" class="ratio-4x5" />
                    @if (! empty($person['quote']))
                        <blockquote class="leader-quote"><i class="fa-solid fa-quote-left"></i>{{ $person['quote'] }}</blockquote>
                    @endif
                </div>
                <div class="leader-body">
                    <span class="eyebrow">{{ $person['designation'] ?? '' }}@if (! empty($person['location'])) · {{ $person['location'] }}@endif</span>
                    <h2 class="leader-name">{{ $person['name'] ?? '' }}</h2>
                    @if (! empty($person['title']))<h3 class="sub-title-sm">{{ $person['title'] }}</h3>@endif
                    <div class="prose">{{ rich_text($person['summary'] ?? '') }}</div>
                    @if ($hl = cms_lines($person['highlights'] ?? ''))
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
<section class="section bg-soft" id="sec-team">
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
