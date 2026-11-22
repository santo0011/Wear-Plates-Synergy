{{-- Inner-page banner driven by a CMS "hero" section. --}}
@props(['section' => null, 'title' => null, 'subtitle' => null, 'image' => null, 'crumbs' => []])
@php
    $title = $title ?? $section?->title;
    $subtitle = $subtitle ?? $section?->subtitle;
    $image = $image ?? $section?->image;
@endphp
<section @class(['page-hero', 'has-image' => $image]) id="sec-hero">
    @if ($image)
        <div class="page-hero-bg" style="background-image:url('{{ asset($image) }}')" aria-hidden="true"></div>
    @endif
    <div class="page-hero-grid" aria-hidden="true"></div>
    <div class="container position-relative">
        <nav aria-label="Breadcrumb">
            <ol class="crumbs">
                <li><a href="{{ route('home') }}"><i class="fa-solid fa-house" aria-hidden="true"></i><span class="visually-hidden">Home</span></a></li>
                @foreach ($crumbs as $label => $url)
                    <li><a href="{{ $url }}">{{ $label }}</a></li>
                @endforeach
                <li aria-current="page">{{ plain_text($title) }}</li>
            </ol>
        </nav>
        <h1 class="page-hero-title">{{ heading_text($title) }}</h1>
        @if ($subtitle)<p class="page-hero-sub">{{ $subtitle }}</p>@endif
        {{ $slot }}
    </div>
</section>
