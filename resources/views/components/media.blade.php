{{--
    Image with a branded placeholder. Every website image is uploaded from the admin panel;
    until then a neutral steel-pattern placeholder with an icon keeps the layout intact.
    <x-media :src="$path" alt="..." icon="fa-solid fa-industry" class="ratio-4x3" />
--}}
@props(['src' => null, 'alt' => '', 'icon' => 'fa-solid fa-industry', 'eager' => false, 'fit' => 'cover'])
@if ($src)
    <figure {{ $attributes->class(['media', 'media-contain' => $fit === 'contain']) }}>
        <img src="{{ asset($src) }}" alt="{{ $alt }}" @unless ($eager) loading="lazy" @endunless decoding="async">
    </figure>
@else
    <figure {{ $attributes->class(['media', 'media-ph']) }} role="img" aria-label="{{ $alt }}">
        <span class="media-ph-icon"><i class="{{ $icon }}" aria-hidden="true"></i></span>
    </figure>
@endif
