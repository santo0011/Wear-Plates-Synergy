<div class="contact-info-card">
    <span class="eyebrow text-gold">Contact</span>
    <h3 class="mb-3">{{ setting('site_name', config('app.name')) }}</h3>
    <p class="mb-3">Reach out for product enquiries, quotations and technical support.</p>

    @if ($phone = setting('phone'))
        <div class="contact-item">
            <i class="fa-solid fa-phone"></i>
            <div><small>Mobile</small><a href="{{ tel_url($phone) }}">{{ $phone }}</a></div>
        </div>
    @endif
    @if ($wa = whatsapp_url('Hello '.setting('site_name', config('app.name')).', I have an enquiry.'))
        <div class="contact-item">
            <i class="fa-brands fa-whatsapp"></i>
            <div><small>WhatsApp</small><a href="{{ $wa }}" target="_blank" rel="noopener">Chat with us on WhatsApp</a></div>
        </div>
    @endif
    @if ($email = setting('email'))
        <div class="contact-item">
            <i class="fa-solid fa-envelope"></i>
            <div><small>Email</small><a href="mailto:{{ $email }}">{{ $email }}</a></div>
        </div>
    @endif
    @if ($corp = setting('corporate_office'))
        <div class="contact-item">
            <i class="fa-solid fa-building"></i>
            <div><small>Corporate Office</small><strong>{{ $corp }}</strong></div>
        </div>
    @endif
    @if ($plant = setting('plant_location'))
        <div class="contact-item">
            <i class="fa-solid fa-industry"></i>
            <div><small>Manufacturing Plant</small><strong>{{ $plant }}</strong>
                @if ($addr = setting('plant_address'))<div class="small mt-1">{!! nl2br(e($addr)) !!}</div>@endif
            </div>
        </div>
    @endif
    @if ($gst = setting('gstin'))
        <div class="contact-item">
            <i class="fa-solid fa-file-invoice"></i>
            <div><small>GSTIN</small><strong>{{ $gst }}</strong></div>
        </div>
    @endif
    @if ($hours = setting('business_hours'))
        <div class="contact-item">
            <i class="fa-solid fa-clock"></i>
            <div><small>Business Hours</small><strong>{{ $hours }}</strong></div>
        </div>
    @endif
</div>
