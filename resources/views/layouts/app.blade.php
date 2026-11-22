@php
    $siteName = setting('site_name', config('app.name'));
    $pageTitle = trim($__env->yieldContent('title'));
    $fullTitle = $pageTitle !== '' ? $pageTitle.' | '.setting('meta_title_suffix', $siteName) : setting('seo_home_title', $siteName);
    $metaDescription = trim($__env->yieldContent('meta_description')) ?: setting('meta_description');
    $canonical = trim($__env->yieldContent('canonical')) ?: url()->current();
    $ogImage = asset(setting('logo') ?: 'images/brand/logo.png');
@endphp
<!DOCTYPE html>
<html lang="en" class="no-js">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $fullTitle }}</title>
    <meta name="description" content="{{ \Illuminate\Support\Str::limit(strip_tags($metaDescription), 300, '') }}">
    @if ($keywords = setting('meta_keywords'))
        <meta name="keywords" content="{{ $keywords }}">
    @endif
    <link rel="canonical" href="{{ $canonical }}">
    <meta name="theme-color" content="#0f1720">

    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{{ $siteName }}">
    <meta property="og:title" content="{{ $fullTitle }}">
    <meta property="og:description" content="{{ \Illuminate\Support\Str::limit(strip_tags($metaDescription), 300, '') }}">
    <meta property="og:url" content="{{ $canonical }}">
    <meta property="og:image" content="{{ $ogImage }}">
    <meta name="twitter:card" content="summary">

    <link rel="icon" href="{{ asset('images/brand/favicon-64.png') }}" type="image/png">
    <link rel="apple-touch-icon" href="{{ asset('images/brand/logo-192.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <link href="{{ asset('css/site.css') }}?v={{ filemtime(public_path('css/site.css')) }}" rel="stylesheet">

    <script type="application/ld+json">
        {!! json_encode(array_filter([
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => $siteName,
            'url' => url('/'),
            'logo' => asset(setting('logo') ?: 'images/brand/logo.png'),
            'telephone' => setting('phone'),
            'email' => setting('email'),
            'foundingDate' => setting('established_year'),
            'slogan' => setting('tagline'),
        ]), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}
    </script>
    @stack('head')
</head>
<body>
    <a class="skip-link" href="#main">Skip to content</a>

    @include('partials.header')

    <main id="main">
        @yield('content')
    </main>

    @include('partials.footer')

    @if ($wa = whatsapp_url('Hello '.$siteName.', I would like to know more about your products.'))
        <a href="{{ $wa }}" class="fab-whatsapp" target="_blank" rel="noopener" aria-label="Chat on WhatsApp">
            <i class="fa-brands fa-whatsapp"></i><span class="fab-tip">Chat on WhatsApp</span>
        </a>
    @endif
    <button type="button" class="back-to-top" aria-label="Back to top"><i class="fa-solid fa-arrow-up"></i></button>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" defer></script>
    <script src="{{ asset('js/site.js') }}?v={{ filemtime(public_path('js/site.js')) }}" defer></script>
    @stack('scripts')
</body>
</html>
