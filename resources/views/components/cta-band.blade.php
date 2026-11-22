@props(['title' => 'Need a Wear Protection Solution?', 'text' => 'Share your application details and our team will get back to you with the right solution.'])
<div class="cta-band reveal">
    <div class="row align-items-center g-4">
        <div class="col-lg-7">
            <h2>{{ $title }}</h2>
            <p>{{ $text }}</p>
        </div>
        <div class="col-lg-5 d-flex flex-wrap gap-2 justify-content-lg-end">
            <a href="{{ route('contact') }}#enquiry" class="btn btn-gold btn-lg"><i class="fa-solid fa-paper-plane me-2"></i>Send Enquiry</a>
            @if ($phone = setting('phone'))
                <a href="{{ tel_url($phone) }}" class="btn btn-outline-light-gold btn-lg"><i class="fa-solid fa-phone me-2"></i>Call Us</a>
            @endif
        </div>
    </div>
</div>
