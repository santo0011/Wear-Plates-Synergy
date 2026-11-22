<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('title', 'Dashboard') | Admin – {{ setting('site_name', config('app.name')) }}</title>
    <link rel="icon" href="{{ asset('images/brand/favicon-64.png') }}" type="image/png">
    <link rel="apple-touch-icon" href="{{ asset('images/brand/logo-192.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <link href="{{ asset('css/admin.css') }}?v={{ filemtime(public_path('css/admin.css')) }}" rel="stylesheet">
</head>
<body>
<script>
    // Restore the collapsed sidebar before first paint (desktop only).
    try { if (localStorage.getItem('wps-sidebar') === 'collapsed' && window.innerWidth >= 992) document.body.classList.add('sidebar-collapsed'); } catch (e) {}
</script>
@php
    $admin = auth('admin')->user();
    $nav = [
        ['admin.dashboard', 'admin.dashboard', 'fa-solid fa-house', 'Dashboard'],
        ['admin.enquiries.index', 'admin.enquiries.*', 'fa-solid fa-inbox', 'Enquiries'],
        ['admin.products.index', 'admin.products.*', 'fa-solid fa-box', 'Products'],
        ['admin.categories.index', 'admin.categories.*', 'fa-solid fa-layer-group', 'Categories'],
        ['admin.locations.index', 'admin.locations.*', 'fa-solid fa-location-dot', 'Locations'],
        ['admin.settings.edit', 'admin.settings.*', 'fa-solid fa-gear', 'Website Settings'],
    ];
@endphp

<aside class="sidebar" id="adminSidebar">
    <button type="button" class="sidebar-close d-lg-none" aria-label="Close menu" onclick="document.body.classList.remove('sidebar-open')"><i class="fa-solid fa-xmark"></i></button>
    <a href="{{ route('admin.dashboard') }}" class="sidebar-brand">
        <img src="{{ asset('images/brand/logo-192.png') }}" alt="{{ setting('site_name', config('app.name')) }} logo" width="44" height="44">
        <span>Admin Panel</span>
    </a>

    <nav class="sidebar-nav">
        @foreach ($nav as [$route, $pattern, $icon, $label])
            <a href="{{ route($route) }}" title="{{ $label }}" @class(['active' => request()->routeIs($pattern)])>
                <i class="{{ $icon }}"></i><span>{{ $label }}</span>
                @if ($route === 'admin.enquiries.index' && $newEnquiryCount)
                    <span class="nav-badge">{{ $newEnquiryCount }}</span>
                @endif
            </a>
        @endforeach
    </nav>

    <div class="sidebar-footer">
        <a href="{{ route('admin.profile.edit') }}" @class(['sidebar-user', 'active' => request()->routeIs('admin.profile.*')])>
            <span class="avatar">{{ strtoupper(mb_substr($admin->name, 0, 1)) }}</span>
            <span class="who"><strong>{{ $admin->name }}</strong><small>{{ $admin->email }}</small></span>
        </a>
        <form method="POST" action="{{ route('admin.logout') }}"
              data-confirm-type="logout" data-confirm-title="Log out?"
              data-confirm="You will need to sign in again to access the admin panel." data-confirm-button="Yes, log out">@csrf
            <button class="sidebar-logout" title="Logout"><i class="fa-solid fa-right-from-bracket"></i><span>Logout</span></button>
        </form>
    </div>
</aside>
<div class="sidebar-backdrop" onclick="document.body.classList.remove('sidebar-open')"></div>

<div class="main">
    <header class="topbar">
        <button class="icon-btn" type="button" id="sidebarToggle" aria-label="Collapse or expand menu" title="Collapse / expand menu"><i class="fa-solid fa-bars"></i></button>
        <h1>@yield('title', 'Dashboard')</h1>
        <div class="ms-auto d-flex align-items-center gap-2">
            @yield('actions')
            <a href="{{ route('home') }}" target="_blank" rel="noopener" class="btn btn-light border d-none d-sm-inline-flex align-items-center gap-2"><i class="fa-solid fa-arrow-up-right-from-square"></i>View Website</a>
        </div>
    </header>

    <main class="content">
        @if ($errors->any())
            <div class="alert alert-danger" role="alert">
                <strong><i class="fa-solid fa-triangle-exclamation me-1"></i>Please fix the following:</strong>
                <ul class="mb-0 mt-1">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif

        @yield('content')
    </main>
</div>

{{-- Success / error notifications (auto-hide) --}}
<div class="toast-stack" aria-live="polite" aria-atomic="true">
    @foreach (['success' => ['fa-circle-check', 'Done'], 'error' => ['fa-circle-exclamation', 'Action not completed']] as $type => [$icon, $heading])
        @if (session($type))
            <div class="toast app-toast {{ $type }}" role="alert" data-bs-delay="{{ $type === 'error' ? 7000 : 4000 }}">
                <div class="app-toast-icon"><i class="fa-solid {{ $icon }}"></i></div>
                <div class="app-toast-body"><strong>{{ $heading }}</strong><span>{{ session($type) }}</span></div>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                <div class="app-toast-progress"></div>
            </div>
        @endif
    @endforeach
</div>

{{-- Confirmation dialog for delete / logout --}}
<div class="modal fade confirm-modal" id="confirmModal" tabindex="-1" aria-labelledby="confirmTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="confirm-icon"><i class="fa-solid fa-trash-can"></i></div>
            <h2 class="confirm-title" id="confirmTitle">Are you sure?</h2>
            <p class="confirm-text" id="confirmText"></p>
            <div class="confirm-actions">
                <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmOk">Yes, delete</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Sidebar toggle: collapse to icons on desktop, slide-in drawer on mobile.
    document.getElementById('sidebarToggle').addEventListener('click', function () {
        var b = document.body;
        if (window.innerWidth < 992) { b.classList.toggle('sidebar-open'); return; }
        var collapsed = b.classList.toggle('sidebar-collapsed');
        try { localStorage.setItem('wps-sidebar', collapsed ? 'collapsed' : 'expanded'); } catch (e) {}
    });
    // Styled confirmation dialog for forms with data-confirm (delete, logout)
    (function () {
        var el = document.getElementById('confirmModal');
        var modal = bootstrap.Modal.getOrCreateInstance(el);
        var okBtn = document.getElementById('confirmOk');
        var pending = null;
        document.querySelectorAll('form[data-confirm]').forEach(function (form) {
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                var logout = form.dataset.confirmType === 'logout';
                pending = form;
                el.classList.toggle('is-logout', logout);
                el.querySelector('.confirm-icon i').className = 'fa-solid ' + (logout ? 'fa-right-from-bracket' : 'fa-trash-can');
                document.getElementById('confirmTitle').textContent = form.dataset.confirmTitle || 'Are you sure?';
                document.getElementById('confirmText').textContent = form.dataset.confirm;
                okBtn.className = 'btn ' + (logout ? 'btn-maroon' : 'btn-danger');
                okBtn.textContent = form.dataset.confirmButton || 'Yes, delete';
                okBtn.disabled = false;
                document.body.classList.remove('sidebar-open');
                modal.show();
            });
        });
        el.addEventListener('shown.bs.modal', function () { okBtn.focus(); });
        okBtn.addEventListener('click', function () {
            if (!pending) return;
            okBtn.disabled = true;
            okBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Please wait…';
            pending.submit(); // native submit skips the listener above
        });
    })();
    // Show flash notifications
    document.querySelectorAll('.app-toast').forEach(function (t) { bootstrap.Toast.getOrCreateInstance(t).show(); });
    // Live preview for image inputs
    document.querySelectorAll('input[type=file][data-preview]').forEach(function (input) {
        input.addEventListener('change', function () {
            var img = document.getElementById(input.getAttribute('data-preview'));
            if (img && input.files && input.files[0]) { img.src = URL.createObjectURL(input.files[0]); img.classList.remove('d-none'); }
        });
    });
    // Auto-generate slug from name when slug is empty
    (function () {
        var name = document.querySelector('[data-slug-source]'), slug = document.querySelector('[data-slug-target]');
        if (!name || !slug) return;
        var touched = slug.value !== '';
        slug.addEventListener('input', function () { touched = slug.value !== ''; });
        name.addEventListener('input', function () {
            if (touched) return;
            slug.placeholder = name.value.toLowerCase().replace(/&/g, 'and').replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '');
        });
    })();
</script>
@stack('scripts')
</body>
</html>
