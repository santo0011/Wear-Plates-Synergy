@props(['eyebrow' => null, 'title', 'subtitle' => null, 'center' => true])
<div {{ $attributes->class(['section-heading reveal', 'text-center' => $center]) }}>
    @if ($eyebrow)
        <span class="eyebrow">{{ $eyebrow }}</span>
    @endif
    <h2 class="section-title">{!! $title !!}</h2>
    @if ($subtitle)
        <p class="section-sub">{{ $subtitle }}</p>
    @endif
    {{ $slot }}
</div>
