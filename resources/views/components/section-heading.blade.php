@props(['eyebrow' => null, 'heading' => null, 'text' => null, 'center' => false, 'light' => false])
<div {{ $attributes->class(['sec-head', 'text-center mx-auto' => $center, 'is-light' => $light]) }}>
    @if ($eyebrow)<span class="eyebrow">{{ $eyebrow }}</span>@endif
    @if ($heading)<h2 class="sec-title">{{ heading_text($heading) }}</h2>@endif
    @if ($text)<div class="sec-lead">{{ rich_text($text) }}</div>@endif
    {{ $slot }}
</div>
