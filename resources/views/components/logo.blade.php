@props(['light' => false])
@php
    // Admin → Website Settings → General & Logos overrides the bundled company badge.
    $logo = setting('logo') ?: 'images/brand/logo-192.png';
    $siteName = setting('site_name', config('app.name'));
@endphp
<a href="{{ route('home') }}" {{ $attributes->class(['brand', 'light' => $light]) }} aria-label="{{ $siteName }} – Home">
    <img src="{{ asset($logo) }}" alt="{{ $siteName }} logo" class="brand-logo-img" width="96" height="96">
    <span class="brand-text">
        <span class="brand-name">Wear Plates <span>Synergy</span></span>
        <span class="brand-sub">VE India &middot; Since {{ setting('established_year', '2008') }}</span>
    </span>
</a>
