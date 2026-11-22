<div class="contact-info-card">
    <h3 class="fs-4 mb-2">Need a Quote?</h3>
    <p class="mb-4">Share your requirement for {{ $subject }} and our team will get back to you.</p>
    <div class="d-grid gap-2">
        <a href="{{ route('contact', isset($productSlug) ? ['product' => $productSlug] : []) }}#enquiry" class="btn btn-gold"><i class="fa-solid fa-paper-plane me-2"></i>Send Enquiry</a>
        @if ($wa = whatsapp_url('Hello, I would like to enquire about: '.$subject))
            <a href="{{ $wa }}" target="_blank" rel="noopener" class="btn btn-whatsapp"><i class="fa-brands fa-whatsapp me-2"></i>WhatsApp Enquiry</a>
        @endif
        @if ($phone = setting('phone'))
            <a href="{{ tel_url($phone) }}" class="btn btn-outline-light-gold"><i class="fa-solid fa-phone me-2"></i>{{ $phone }}</a>
        @endif
    </div>
</div>
