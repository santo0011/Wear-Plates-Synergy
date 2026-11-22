<div class="contact-panel">
    @if ($phone = setting('phone'))
        <div class="contact-item">
            <i class="fa-solid fa-phone"></i>
            <div><small>Call us</small><a href="{{ tel_url($phone) }}">{{ $phone }}</a>@if ($p2 = setting('phone_secondary'))<a href="{{ tel_url($p2) }}">{{ $p2 }}</a>@endif</div>
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
            <div><small>Email</small><a href="mailto:{{ $email }}">{{ $email }}</a>@if ($e2 = setting('email_secondary'))<a href="mailto:{{ $e2 }}">{{ $e2 }}</a>@endif</div>
        </div>
    @endif
    @if ($addr = setting('plant_address'))
        <div class="contact-item">
            <i class="fa-solid fa-industry"></i>
            <div><small>Manufacturing Plant</small><span>{{ $addr }}</span></div>
        </div>
    @endif
    @if ($gst = setting('gstin'))
        <div class="contact-item">
            <i class="fa-solid fa-file-invoice"></i>
            <div><small>GSTIN</small><span>{{ $gst }}</span></div>
        </div>
    @endif
    @if ($hours = setting('business_hours'))
        <div class="contact-item">
            <i class="fa-solid fa-clock"></i>
            <div><small>Business Hours</small><span>{{ $hours }}</span></div>
        </div>
    @endif
</div>
