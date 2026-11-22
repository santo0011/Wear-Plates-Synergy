@props(['product' => null])
@php
    // Company WhatsApp number (Admin → Contact Details). Always the company's number, never the customer's.
    $waNumber = preg_replace('/\D+/', '', (string) setting('whatsapp_number', setting('phone')));
    if (strlen($waNumber) === 10) {
        $waNumber = '91'.$waNumber; // add India country code when it was entered without one
    }
    if (strlen($waNumber) < 11) {
        $waNumber = '917981795006'; // fallback: company number from the profile
    }
    $defaultSubject = $product ? 'Enquiry: '.$product->name : '';
@endphp
<div class="form-card" id="enquiry">
    <span class="eyebrow">Send an Enquiry</span>
    <h2 class="section-title fs-2 mb-2">Request a Quote</h2>
    <p class="text-muted mb-4">Fill in the form and our team will contact you. Fields marked <span class="text-maroon">*</span> are required.</p>

    @if (session('success'))
        <div class="alert alert-success-wps d-flex gap-3 align-items-start" role="alert">
            <i class="fa-solid fa-circle-check fs-4 mt-1"></i>
            <div>
                <strong class="d-block mb-1">Enquiry submitted</strong>
                {{ session('success') }}
                @if (session('whatsapp_followup'))
                    <div class="mt-2"><a href="{{ session('whatsapp_followup') }}" target="_blank" rel="noopener" class="btn btn-whatsapp btn-sm"><i class="fa-brands fa-whatsapp me-1"></i>Also notify us on WhatsApp</a></div>
                @endif
            </div>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger" role="alert">
            <strong><i class="fa-solid fa-triangle-exclamation me-1"></i> Please correct the highlighted fields.</strong>
        </div>
    @endif

    <form id="enquiryForm" action="{{ route('contact.store') }}" method="POST" class="needs-validation" novalidate>
        @csrf
        @if ($product)
            <input type="hidden" name="product_id" value="{{ $product->id }}">
            <div class="product-meta-chip mb-3"><i class="fa-solid fa-box"></i> Enquiring about: {{ $product->name }}</div>
        @elseif (old('product_id'))
            <input type="hidden" name="product_id" value="{{ old('product_id') }}">
        @endif
        <div class="hp-field" aria-hidden="true">
            <label for="website">Leave this field empty</label>
            <input type="text" name="website" id="website" tabindex="-1" autocomplete="off">
        </div>

        <div class="row g-3">
            <div class="col-md-6">
                <label for="name" class="form-label">Full Name <span class="req">*</span></label>
                <div class="input-icon">
                    <i class="fa-solid fa-user"></i>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required minlength="2" maxlength="120" autocomplete="name">
                    <div class="invalid-feedback">@error('name'){{ $message }}@else Please enter your name.@enderror</div>
                </div>
            </div>
            <div class="col-md-6">
                <label for="company_name" class="form-label">Company Name</label>
                <div class="input-icon">
                    <i class="fa-solid fa-building"></i>
                    <input type="text" class="form-control @error('company_name') is-invalid @enderror" id="company_name" name="company_name" value="{{ old('company_name') }}" maxlength="160" autocomplete="organization">
                    <div class="invalid-feedback">@error('company_name'){{ $message }}@enderror</div>
                </div>
            </div>
            <div class="col-md-6">
                <label for="mobile" class="form-label">Mobile Number <span class="req">*</span></label>
                <div class="input-icon">
                    <i class="fa-solid fa-mobile-screen"></i>
                    <input type="tel" class="form-control @error('mobile') is-invalid @enderror" id="mobile" name="mobile" value="{{ old('mobile') }}" required pattern="^\+?[0-9\s\-()]{7,20}$" maxlength="20" autocomplete="tel">
                    <div class="invalid-feedback">@error('mobile'){{ $message }}@else Please enter a valid mobile number.@enderror</div>
                </div>
            </div>
            <div class="col-md-6">
                <label for="email" class="form-label">Email</label>
                <div class="input-icon">
                    <i class="fa-solid fa-envelope"></i>
                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" maxlength="160" autocomplete="email">
                    <div class="invalid-feedback">@error('email'){{ $message }}@else Please enter a valid email address.@enderror</div>
                </div>
            </div>
            <div class="col-12">
                <label for="subject" class="form-label">Subject <span class="req">*</span></label>
                <div class="input-icon">
                    <i class="fa-solid fa-tag"></i>
                    <input type="text" class="form-control @error('subject') is-invalid @enderror" id="subject" name="subject" value="{{ old('subject', $defaultSubject) }}" required minlength="3" maxlength="200">
                    <div class="invalid-feedback">@error('subject'){{ $message }}@else Please enter a subject.@enderror</div>
                </div>
            </div>
            <div class="col-12">
                <label for="message" class="form-label">Message <span class="req">*</span></label>
                <textarea class="form-control @error('message') is-invalid @enderror" id="message" name="message" rows="5" required minlength="10" maxlength="3000" placeholder="Tell us about your application, material, quantity or requirement…">{{ old('message') }}</textarea>
                <div class="invalid-feedback">@error('message'){{ $message }}@else Please enter a message (at least 10 characters).@enderror</div>
            </div>
            <div class="col-12 d-flex flex-wrap gap-2 pt-2">
                <button type="submit" class="btn btn-maroon btn-lg"><i class="fa-solid fa-paper-plane me-2"></i>Submit Enquiry</button>
                <button type="button" class="btn btn-whatsapp btn-lg" data-whatsapp-form="enquiryForm" data-number="{{ $waNumber }}"
                        data-company="{{ setting('site_name', config('app.name')) }}" @if ($product) data-product="{{ $product->name }}" @endif>
                    <i class="fa-brands fa-whatsapp me-2"></i>Send via WhatsApp
                </button>
            </div>
            <div class="col-12">
                <div class="wa-feedback small" data-whatsapp-feedback role="status" aria-live="polite"></div>
                <p class="small text-muted mb-0 mt-1"><i class="fa-brands fa-whatsapp me-1 text-success"></i>"Send via WhatsApp" opens WhatsApp with your details filled in. Review the message and press send in WhatsApp.</p>
            </div>
        </div>
    </form>
</div>
