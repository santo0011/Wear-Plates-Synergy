@php
    $siteName = setting('site_name', config('app.name'));
    $isoLabel = setting('iso_label', 'ISO 9001:2015');
    $certs = array_filter([
        setting('make_in_india') ? ['make-in-india', setting('make_in_india_logo'), 'Make in India'] : null,
        setting('iso_certified') ? ['iso', setting('iso_logo'), $isoLabel.' Certified'] : null,
        setting('msme_registered') && setting('msme_logo') ? ['msme', setting('msme_logo'), 'MSME Registered'] : null,
    ]);
    $companyActive = request()->routeIs('about', 'leadership', 'why', 'presence');
    $capActive = request()->routeIs('infrastructure', 'quality', 'resources');
@endphp
<div class="topbar d-none d-lg-block">
    <div class="container-wide d-flex justify-content-between align-items-center gap-3">
        <span class="topbar-motto"><i class="fa-solid fa-award me-2" aria-hidden="true"></i>{{ setting('motto', 'Expertise to Lead, Lead to the Bench Marking') }}</span>
        <div class="d-flex align-items-center">
            @if ($phone = setting('phone'))
                <a href="{{ tel_url($phone) }}"><i class="fa-solid fa-phone" aria-hidden="true"></i>{{ $phone }}</a>
            @endif
            @if ($email = setting('email'))
                <span class="sep" aria-hidden="true"></span>
                <a href="mailto:{{ $email }}"><i class="fa-solid fa-envelope" aria-hidden="true"></i>{{ $email }}</a>
            @endif
            @if ($plant = setting('plant_location'))
                <span class="sep" aria-hidden="true"></span>
                <span><i class="fa-solid fa-industry" aria-hidden="true"></i>Plant: {{ $plant }}</span>
            @endif
        </div>
    </div>
</div>

<header class="site-header">
    <nav class="navbar navbar-expand-xl" aria-label="Main navigation">
        <div class="container-wide">
            <div class="brand-cluster">
                <x-logo class="navbar-brand" />
                @if ($strip = setting('header_logo_strip'))
                    {{-- One uploaded image containing all certification logos (header only). --}}
                    <div class="cert-logos is-strip">
                        <img src="{{ asset($strip) }}" alt="Make in India · {{ $isoLabel }} Certified · MSME Registered" class="cert-strip">
                    </div>
                @elseif ($certs)
                    <div class="cert-logos" aria-label="Certifications">
                        @foreach ($certs as [$type, $logo, $label])
                            @if ($logo)
                                <img src="{{ asset($logo) }}" alt="{{ $label }}" class="cert-logo cert-{{ $type }}" title="{{ $label }}" height="48">
                            @elseif ($type === 'iso')
                                <span class="cert-badge cert-badge-iso" title="{{ $label }}"><strong>ISO</strong><small>{{ \Illuminate\Support\Str::after($isoLabel, 'ISO ') ?: '9001' }}</small></span>
                            @else
                                <span class="cert-badge cert-badge-mii" title="{{ $label }}"><i class="fa-solid fa-gear" aria-hidden="true"></i><span>Make in<b>India</b></span></span>
                            @endif
                        @endforeach
                    </div>
                @endif
            </div>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="toggler-bars" aria-hidden="true"><i></i><i></i><i></i></span>
            </button>

            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav ms-auto align-items-xl-center">
                    <li class="nav-item"><a @class(['nav-link', 'active' => request()->routeIs('home')]) href="{{ route('home') }}">Home</a></li>

                    <li class="nav-item dropdown">
                        <a @class(['nav-link dropdown-toggle', 'active' => $companyActive]) href="{{ route('about') }}" role="button" data-bs-toggle="dropdown" aria-expanded="false">Company</a>
                        <div class="dropdown-menu dd-panel">
                            <a class="dd-link" href="{{ route('about') }}"><i class="fa-solid fa-building"></i><span><strong>About Us</strong><small>Profile, mission &amp; values, milestones</small></span></a>
                            <a class="dd-link" href="{{ route('leadership') }}"><i class="fa-solid fa-user-tie"></i><span><strong>Leadership</strong><small>Management team &amp; experience</small></span></a>
                            <a class="dd-link" href="{{ route('why') }}"><i class="fa-solid fa-thumbs-up"></i><span><strong>Why Choose Us</strong><small>Our strengths &amp; promise</small></span></a>
                            <a class="dd-link" href="{{ route('presence') }}"><i class="fa-solid fa-map-location-dot"></i><span><strong>Our Presence</strong><small>Plant &amp; offices across India</small></span></a>
                        </div>
                    </li>

                    <li class="nav-item dropdown dropdown-mega">
                        <a @class(['nav-link dropdown-toggle', 'active' => request()->routeIs('products.*')]) href="{{ route('products.index') }}" role="button" data-bs-toggle="dropdown" aria-expanded="false">Products</a>
                        <div class="dropdown-menu mega-panel">
                            <div class="mega-grid">
                                @foreach ($navCategories as $navCategory)
                                    <a class="mega-link" href="{{ route('products.category', $navCategory) }}">
                                        <span class="mega-icon"><i class="{{ $navCategory->icon_class }}"></i></span>
                                        <span><strong>{{ $navCategory->name }}</strong><small>{{ \Illuminate\Support\Str::limit($navCategory->short_description, 70) }}</small></span>
                                    </a>
                                @endforeach
                            </div>
                            <div class="mega-foot">
                                <span><i class="fa-solid fa-circle-info me-2"></i>Not sure which material fits your application?</span>
                                <a href="{{ route('products.index') }}" class="btn btn-sm btn-outline-brand">All Products</a>
                                <a href="{{ route('contact') }}#enquiry" class="btn btn-sm btn-brand">Ask an Engineer</a>
                            </div>
                        </div>
                    </li>

                    <li class="nav-item"><a @class(['nav-link', 'active' => request()->routeIs('industries')]) href="{{ route('industries') }}">Industries</a></li>

                    <li class="nav-item dropdown">
                        <a @class(['nav-link dropdown-toggle', 'active' => $capActive]) href="{{ route('infrastructure') }}" role="button" data-bs-toggle="dropdown" aria-expanded="false">Capabilities</a>
                        <div class="dropdown-menu dd-panel">
                            <a class="dd-link" href="{{ route('infrastructure') }}"><i class="fa-solid fa-warehouse"></i><span><strong>Infrastructure</strong><small>Plant, machinery &amp; capacity</small></span></a>
                            <a class="dd-link" href="{{ route('quality') }}"><i class="fa-solid fa-award"></i><span><strong>Quality &amp; Certifications</strong><small>ISO 9001:2015, QAP &amp; testing</small></span></a>
                            <a class="dd-link" href="{{ route('resources') }}"><i class="fa-solid fa-book-open"></i><span><strong>Technical Resources</strong><small>Hardfacing, wear &amp; data sheets</small></span></a>
                        </div>
                    </li>

                    <li class="nav-item"><a @class(['nav-link', 'active' => request()->routeIs('projects')]) href="{{ route('projects') }}">Projects</a></li>
                    <li class="nav-item"><a @class(['nav-link', 'active' => request()->routeIs('contact')]) href="{{ route('contact') }}">Contact</a></li>
                    <li class="nav-item nav-cta">
                        <a class="btn btn-brand" href="{{ route('contact') }}#enquiry">Get a Quote<i class="fa-solid fa-arrow-right ms-2" aria-hidden="true"></i></a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</header>
