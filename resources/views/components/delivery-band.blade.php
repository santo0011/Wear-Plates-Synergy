@php
    $partners = array_filter(array_map('trim', explode('|', (string) setting('delivery_partners', ''))));
@endphp
<div class="delivery reveal reveal-zoom">
    <div class="row align-items-center g-5">
        <div class="col-lg-6">
            <span class="eyebrow text-gold">Shipping Across India</span>
            <h2>{{ setting('delivery_heading', 'Delivery & Logistics') }}</h2>
            @if ($partners)
                <div class="partner-chips">
                    @foreach ($partners as $partner)
                        <span class="partner-chip">{{ $partner }}</span>
                    @endforeach
                </div>
            @endif
            <p class="delivery-text">{{ setting('delivery_text') }}</p>
            <div class="mode-pills mt-4">
                <span class="mode-pill"><i class="fa-solid fa-plane-departure"></i>Air Transport</span>
                <span class="mode-pill"><i class="fa-solid fa-truck-fast"></i>Surface Transport</span>
                @if ($plant = setting('plant_location'))
                    <span class="mode-pill"><i class="fa-solid fa-location-dot"></i>Dispatch: {{ $plant }}</span>
                @endif
            </div>
        </div>
        <div class="col-lg-6 delivery-visual">
            <img src="{{ asset('images/logistics.svg') }}" alt="Air and surface transport from the Ankleshwar plant" loading="lazy" width="640" height="400">
        </div>
    </div>
</div>
