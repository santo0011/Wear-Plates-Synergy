<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('title') | {{ setting('site_name', config('app.name')) }}</title>
    <link rel="icon" href="{{ asset('images/brand/favicon-64.png') }}" type="image/png">
    <link rel="apple-touch-icon" href="{{ asset('images/brand/logo-192.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@600;700;800&family=Inter:wght@400;500;600;700&family=Cinzel:wght@600;700;800&family=Cormorant+Garamond:ital,wght@1,500;1,600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <link href="{{ asset('css/admin.css') }}?v={{ filemtime(public_path('css/admin.css')) }}" rel="stylesheet">
</head>
<body class="auth-body">
@php
    $siteName = setting('site_name', config('app.name'));
    $trust = array_filter(['Since '.setting('established_year', '2008'), setting('iso_certified') ? 'ISO Certified' : null, setting('msme_registered') ? 'MSME Registered' : null]);
    // Background: an uploaded photo (Admin → Website Settings → General) or a collage of product images.
    $loginImage = setting('login_image');
    $collage = [];
    if (! $loginImage) {
        try {
            $collage = \App\Models\ProductCategory::whereNotNull('image')->orderBy('sort_order')->pluck('image')->all();
        } catch (\Throwable) {
            $collage = [];
        }
        $collage = $collage ? array_slice(array_merge($collage, $collage, $collage), 0, 12) : [];
    }
@endphp
<div class="auth">
    <aside class="auth-brand">
        <div class="auth-photo" aria-hidden="true">
            @if ($loginImage)
                <img class="single" src="{{ asset($loginImage) }}" alt="">
            @elseif ($collage)
                <div class="auth-collage">
                    @foreach ($collage as $img)
                        <img src="{{ asset($img) }}" alt="" loading="lazy">
                    @endforeach
                </div>
            @endif
        </div>
        <div class="auth-overlay" aria-hidden="true"></div>

        <div class="auth-brand-inner">
            <div class="auth-logo">
                <span class="ring r1" aria-hidden="true"></span>
                <span class="ring r2" aria-hidden="true"><i></i></span>
                <img src="{{ asset(setting('logo') ?: 'images/brand/logo-192.png') }}" alt="{{ $siteName }} logo" width="150" height="150">
            </div>
            <h1 class="auth-brand-title">Wear Plates <span>Synergy</span></h1>
            <p class="auth-brand-sub">VE India</p>
            <p class="auth-brand-tag">{{ setting('tagline', 'The Leader in Wear Protection') }}</p>
            <ul class="auth-trust">
                @foreach ($trust as $item)
                    <li><i class="fa-solid fa-circle-check"></i>{{ $item }}</li>
                @endforeach
            </ul>
        </div>
        <p class="auth-brand-foot">&copy; {{ date('Y') }} {{ $siteName }}</p>
    </aside>

    <main class="auth-main">
        <div class="auth-card">
            @if (session('success'))
                <div class="auth-alert ok" role="status"><i class="fa-solid fa-circle-check"></i><span>{{ session('success') }}</span></div>
            @endif
            @yield('form')
        </div>
    </main>
</div>

<script>
    (function () {
        // Show / hide password buttons
        document.querySelectorAll('[data-toggle-password]').forEach(function (btn) {
            var input = document.getElementById(btn.getAttribute('data-toggle-password'));
            btn.addEventListener('click', function () {
                var show = input.type === 'password';
                input.type = show ? 'text' : 'password';
                btn.innerHTML = '<i class="fa-regular ' + (show ? 'fa-eye-slash' : 'fa-eye') + '"></i>';
                btn.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
                btn.title = show ? 'Hide password' : 'Show password';
                input.focus();
            });
        });
        // Caps Lock warning
        document.querySelectorAll('[data-caps-for]').forEach(function (warn) {
            var input = document.getElementById(warn.getAttribute('data-caps-for'));
            ['keydown', 'keyup'].forEach(function (ev) {
                input.addEventListener(ev, function (e) { if (e.getModifierState) warn.hidden = !e.getModifierState('CapsLock'); });
            });
        });
        // Client-side required check + loading state
        document.querySelectorAll('form[data-auth-form]').forEach(function (form) {
            var btn = form.querySelector('[type="submit"]');
            form.addEventListener('submit', function (e) {
                var bad = false;
                form.querySelectorAll('input[required]').forEach(function (f) {
                    var ok = f.value.trim() !== '' && f.checkValidity();
                    f.closest('.auth-input').classList.toggle('is-invalid', !ok);
                    if (!ok && !bad) { f.focus(); bad = true; }
                });
                if (bad) { e.preventDefault(); return; }
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span><span class="lbl">' + (btn.getAttribute('data-loading') || 'Please wait…') + '</span>';
            });
            form.querySelectorAll('input').forEach(function (f) {
                f.addEventListener('input', function () { var w = f.closest('.auth-input'); if (w) w.classList.remove('is-invalid'); });
            });
        });
    })();
</script>
@stack('scripts')
</body>
</html>
