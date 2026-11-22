@php
    $partners = array_filter(array_map('trim', explode('|', (string) setting('delivery_partners', ''))));
@endphp
<section class="section-sm">
    <div class="container-wide">
        <div class="delivery reveal">
            <div class="delivery-copy">
                <span class="eyebrow is-gold">Shipping Across India</span>
                <h2>{{ setting('delivery_heading', 'Delivery & Logistics') }}</h2>
                <p>{{ setting('delivery_text') }}</p>
                <div class="mode-pills">
                    <span><i class="fa-solid fa-plane-departure"></i>Air Transport</span>
                    <span><i class="fa-solid fa-truck-fast"></i>Surface Transport</span>
                    @if ($plant = setting('plant_location'))<span><i class="fa-solid fa-location-dot"></i>Dispatch: {{ $plant }}</span>@endif
                </div>
            </div>
            @if ($partners)
                <div class="delivery-partners">
                    <small>Our freight forwarders</small>
                    <div>
                        @foreach ($partners as $partner)<span class="partner-chip">{{ $partner }}</span>@endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</section>
