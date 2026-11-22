<div class="topbar d-none d-lg-block">
    <div class="container d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center flex-wrap">
            @if ($phone = setting('phone'))
                <a href="{{ tel_url($phone) }}"><i class="fa-solid fa-phone me-2 text-gold"></i>{{ $phone }}</a>
            @endif
            @if ($email = setting('email'))
                <span class="sep"></span>
                <a href="mailto:{{ $email }}"><i class="fa-solid fa-envelope me-2 text-gold"></i>{{ $email }}</a>
            @endif
            @if ($plant = setting('plant_location'))
                <span class="sep"></span>
                <span><i class="fa-solid fa-industry me-2 text-gold"></i>Plant: {{ $plant }}</span>
            @endif
        </div>
        <div class="d-flex align-items-center gap-2">
            @if (setting('iso_certified'))<span class="badge-cert"><i class="fa-solid fa-certificate me-1"></i>ISO Certified</span>@endif
            @if (setting('msme_registered'))<span class="badge-cert"><i class="fa-solid fa-building-circle-check me-1"></i>MSME Registered</span>@endif
            @if ($gst = setting('gstin'))<span class="sep"></span><span>GSTIN: {{ $gst }}</span>@endif
        </div>
    </div>
</div>

<header class="site-header">
    <nav class="navbar navbar-expand-xl" aria-label="Main navigation">
        <div class="container">
            <x-logo class="navbar-brand me-4" />

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
                <i class="fa-solid fa-bars-staggered fs-3 text-maroon"></i>
            </button>

            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav ms-auto align-items-xl-center">
                    <li class="nav-item"><a class="nav-link @if (request()->routeIs('home')) active @endif" href="{{ route('home') }}">Home</a></li>
                    <li class="nav-item"><a class="nav-link @if (request()->routeIs('about')) active @endif" href="{{ route('about') }}">About Us</a></li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle @if (request()->routeIs('products.*')) active @endif" href="{{ route('products.index') }}" role="button" data-bs-toggle="dropdown" aria-expanded="false">Products &amp; Solutions</a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item @if (request()->routeIs('products.index')) active @endif" href="{{ route('products.index') }}"><i class="fa-solid fa-grip"></i>All Products &amp; Solutions</a></li>
                            <li><hr class="dropdown-divider"></li>
                            @foreach ($navCategories as $navCategory)
                                <li><a class="dropdown-item @if (request()->route('category')?->is($navCategory)) active @endif" href="{{ route('products.category', $navCategory) }}"><i class="{{ $navCategory->icon_class }}"></i>{{ $navCategory->name }}</a></li>
                            @endforeach
                        </ul>
                    </li>
                    <li class="nav-item"><a class="nav-link @if (request()->routeIs('why')) active @endif" href="{{ route('why') }}">Why Choose Us</a></li>
                    <li class="nav-item"><a class="nav-link @if (request()->routeIs('presence')) active @endif" href="{{ route('presence') }}">Our Presence</a></li>
                    <li class="nav-item"><a class="nav-link @if (request()->routeIs('contact')) active @endif" href="{{ route('contact') }}">Contact Us</a></li>
                    <li class="nav-item ms-xl-3 mt-3 mt-xl-0">
                        <a class="btn btn-maroon" href="{{ route('contact') }}#enquiry"><i class="fa-solid fa-paper-plane me-2"></i>Get a Quote</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</header>
