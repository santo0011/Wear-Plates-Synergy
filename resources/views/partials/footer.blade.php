<footer class="site-footer">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-4">
                <x-logo :light="true" />
                <p class="mt-4 mb-0">{{ setting('footer_about') }}</p>
                <div class="footer-badges">
                    <span>Since {{ setting('established_year', '2008') }}</span>
                    @if (setting('iso_certified'))<span>ISO Certified</span>@endif
                    @if (setting('msme_registered'))<span>MSME Registered</span>@endif
                </div>
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

            <div class="col-6 col-lg-2">
                <h5>Company</h5>
                <ul class="footer-links">
                    <li><a href="{{ route('home') }}"><i class="fa-solid fa-chevron-right"></i>Home</a></li>
                    <li><a href="{{ route('about') }}"><i class="fa-solid fa-chevron-right"></i>About Us</a></li>
                    <li><a href="{{ route('why') }}"><i class="fa-solid fa-chevron-right"></i>Why Choose Us</a></li>
                    <li><a href="{{ route('presence') }}"><i class="fa-solid fa-chevron-right"></i>Our Presence</a></li>
                    <li><a href="{{ route('contact') }}"><i class="fa-solid fa-chevron-right"></i>Contact Us</a></li>
                </ul>
            </div>

            <div class="col-6 col-lg-3">
                <h5>Products</h5>
                <ul class="footer-links">
                    @foreach ($navCategories as $navCategory)
                        <li><a href="{{ route('products.category', $navCategory) }}"><i class="fa-solid fa-chevron-right"></i>{{ $navCategory->name }}</a></li>
                    @endforeach
                </ul>
            </div>

            <div class="col-lg-3">
                <h5>Get in Touch</h5>
                <ul class="list-unstyled footer-contact mb-0">
                    @if ($phone = setting('phone'))
                        <li><i class="fa-solid fa-phone"></i><a href="{{ tel_url($phone) }}">{{ $phone }}</a></li>
                    @endif
                    @if ($wa = whatsapp_url())
                        <li><i class="fa-brands fa-whatsapp"></i><a href="{{ $wa }}" target="_blank" rel="noopener">WhatsApp Enquiry</a></li>
                    @endif
                    @if ($email = setting('email'))
                        <li><i class="fa-solid fa-envelope"></i><a href="mailto:{{ $email }}">{{ $email }}</a></li>
                    @endif
                    @if ($corp = setting('corporate_office'))
                        <li><i class="fa-solid fa-building"></i><span>Corporate Office: {{ $corp }}</span></li>
                    @endif
                    @if ($plant = setting('plant_location'))
                        <li><i class="fa-solid fa-industry"></i><span>Manufacturing Plant: {{ $plant }}</span></li>
                    @endif
                    @if ($gst = setting('gstin'))
                        <li><i class="fa-solid fa-file-invoice"></i><span>GSTIN: {{ $gst }}</span></li>
                    @endif
                </ul>
            </div>
        </div>
    </div>
    <div class="footer-bottom">
        <div class="container d-flex flex-column flex-md-row justify-content-between gap-2">
            <span>&copy; {{ date('Y') }} {{ setting('site_name', config('app.name')) }}. All rights reserved.</span>
            <span>{{ setting('tagline') }} &middot; <a href="{{ route('sitemap') }}">Sitemap</a></span>
        </div>
    </div>
</footer>
