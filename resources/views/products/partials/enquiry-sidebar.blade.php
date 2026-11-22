<div class="quote-card">
    <span class="quote-card-ico"><i class="fa-solid fa-file-signature" aria-hidden="true"></i></span>
    <h3>Need a Quote?</h3>
    <p>Share your requirement for <strong>{{ $subject }}</strong> – drawing, size or application – and our engineers will get back to you.</p>
    <div class="d-grid gap-2">
        <a href="{{ route('contact', isset($productSlug) ? ['product' => $productSlug] : []) }}#enquiry" class="btn btn-gold"><i class="fa-solid fa-paper-plane me-2"></i>Send Enquiry</a>
        @if ($wa = whatsapp_url('Hello, I would like to enquire about: '.$subject))
            <a href="{{ $wa }}" target="_blank" rel="noopener" class="btn btn-whatsapp"><i class="fa-brands fa-whatsapp me-2"></i>WhatsApp Enquiry</a>
        @endif
        @if ($phone = setting('phone'))
            <a href="{{ tel_url($phone) }}" class="btn btn-outline-light"><i class="fa-solid fa-phone me-2"></i>{{ $phone }}</a>
        @endif
    </div>
</div>
