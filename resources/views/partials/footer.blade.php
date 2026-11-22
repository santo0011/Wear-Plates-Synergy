@php
    $siteName = setting('site_name', config('app.name'));
    $isoLabel = setting('iso_label', 'ISO 9001:2015');
@endphp
<footer class="site-footer">
    @unless (request()->routeIs('home', 'contact'))
    <div class="footer-cta">
        <div class="container-wide">
            <div class="footer-cta-inner">
                <div>
                    <span class="eyebrow is-gold">Talk to an engineer</span>
                    <h2>Need the right material for the <span class="hl">right place?</span></h2>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('contact') }}#enquiry" class="btn btn-gold btn-lg">Request a Quote<i class="fa-solid fa-arrow-right ms-2"></i></a>
                    @if ($wa = whatsapp_url('Hello '.$siteName.', I would like to know more about your products.'))
                        <a href="{{ $wa }}" class="btn btn-outline-light btn-lg" target="_blank" rel="noopener"><i class="fa-brands fa-whatsapp me-2"></i>WhatsApp</a>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endunless

    <div class="container-wide footer-main">
        <div class="row g-5">
            <div class="col-lg-4">
                <x-logo :light="true" />
                <p class="mt-4">{{ setting('footer_about') }}</p>
                <div class="footer-certs">
                    @if (setting('iso_certified'))
                        @if ($iso = setting('iso_logo'))<img src="{{ asset($iso) }}" alt="{{ $isoLabel }} Certified" height="52">@else<span class="cert-badge cert-badge-iso"><strong>ISO</strong><small>{{ \Illuminate\Support\Str::after($isoLabel, 'ISO ') }}</small></span>@endif
                    @endif
                    @if (setting('make_in_india'))
                        @if ($mii = setting('make_in_india_logo'))<img src="{{ asset($mii) }}" alt="Make in India" height="52">@else<span class="cert-badge cert-badge-mii"><i class="fa-solid fa-gear"></i><span>Make in<b>India</b></span></span>@endif
                    @endif
                    @if (setting('msme_registered'))
                        @if ($msme = setting('msme_logo'))<img src="{{ asset($msme) }}" alt="MSME Registered" height="52">@else<span class="cert-chip">MSME<br>Registered</span>@endif
                    @endif
                </div>
            </div>

            <div class="col-6 col-md-4 col-lg-2">
                <h3 class="footer-h">Company</h3>
                <ul class="footer-links">
                    <li><a href="{{ route('about') }}">About Us</a></li>
                    <li><a href="{{ route('leadership') }}">Leadership</a></li>
                    <li><a href="{{ route('why') }}">Why Choose Us</a></li>
                    <li><a href="{{ route('presence') }}">Our Presence</a></li>
                    <li><a href="{{ route('projects') }}">Projects</a></li>
                    <li><a href="{{ route('contact') }}">Contact Us</a></li>
                </ul>
            </div>

            <div class="col-6 col-md-4 col-lg-3">
                <h3 class="footer-h">Products</h3>
                <ul class="footer-links">
                    @foreach ($navCategories as $navCategory)
                        <li><a href="{{ route('products.category', $navCategory) }}">{{ $navCategory->name }}</a></li>
                    @endforeach
                </ul>
            </div>

            <div class="col-md-4 col-lg-3">
                <h3 class="footer-h">Get in Touch</h3>
                <ul class="footer-contact">
                    @if ($addr = setting('registered_office'))
                        <li><i class="fa-solid fa-location-dot"></i><span>{{ $addr }}</span></li>
                    @endif
                    @if ($phone = setting('phone'))
                        <li><i class="fa-solid fa-phone"></i><span><a href="{{ tel_url($phone) }}">{{ $phone }}</a>@if ($p2 = setting('phone_secondary'))<br><a href="{{ tel_url($p2) }}">{{ $p2 }}</a>@endif</span></li>
                    @endif
                    @if ($email = setting('email'))
                        <li><i class="fa-solid fa-envelope"></i><span><a href="mailto:{{ $email }}">{{ $email }}</a>@if ($e2 = setting('email_secondary'))<br><a href="mailto:{{ $e2 }}">{{ $e2 }}</a>@endif</span></li>
                    @endif
                    @if ($gst = setting('gstin'))
                        <li><i class="fa-solid fa-file-invoice"></i><span>GSTIN: {{ $gst }}</span></li>
                    @endif
                </ul>
                @php
                    $socials = array_filter([
                        'linkedin-in' => setting('social_linkedin'),
                        'facebook-f' => setting('social_facebook'),
                        'instagram' => setting('social_instagram'),
                        'youtube' => setting('social_youtube'),
                    ]);
                @endphp
                @if ($socials)
                    <div class="social-links">
                        @foreach ($socials as $icon => $url)
                            <a href="{{ $url }}" target="_blank" rel="noopener" aria-label="{{ ucfirst(strtok($icon, '-')) }}"><i class="fa-brands fa-{{ $icon }}"></i></a>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="footer-bottom">
        <div class="container-wide d-flex flex-column flex-md-row justify-content-between gap-2">
            <span>&copy; {{ date('Y') }} {{ $siteName }}. All rights reserved.</span>
            <span class="footer-motto">{{ setting('motto', 'Expertise to Lead, Lead to the Bench Marking') }} &middot; <a href="{{ route('sitemap') }}">Sitemap</a></span>
        </div>
    </div>
</footer>
