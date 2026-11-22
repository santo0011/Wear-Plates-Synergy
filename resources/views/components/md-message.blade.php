@php
    $mdName = setting('md_name');
    $mdTitle = setting('md_title', 'Managing Director');
    $mdPhoto = setting('md_photo');
@endphp
<div class="md-card reveal">
    <div class="row g-0">
        <div class="col-md-4">
            <div class="md-photo">
                @if ($mdPhoto)
                    <img src="{{ asset($mdPhoto) }}" alt="{{ $mdName ?: $mdTitle }}" loading="lazy">
                @else
                    <span class="md-photo-placeholder"><i class="fa-solid fa-user-tie"></i></span>
                @endif
            </div>
        </div>
        <div class="col-md-8">
            <div class="md-body">
                <span class="eyebrow">Managing Director's Message</span>
                <div class="md-quote-icon"><i class="fa-solid fa-quote-left"></i></div>
                <p class="md-message">{{ setting('md_message') }}</p>
                <div class="md-sign">
                    <div>
                        @if ($mdName)
                            <strong>{{ $mdName }}</strong>
                            <span>{{ $mdTitle }}, {{ setting('site_name', config('app.name')) }}</span>
                        @else
                            <strong>{{ $mdTitle }}</strong>
                            <span>{{ setting('site_name', config('app.name')) }}</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
