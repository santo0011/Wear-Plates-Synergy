@props(['product'])
@php
    $url = route('products.show', [$product->category, $product]);
    $wa = whatsapp_url('Hello, I would like to enquire about: '.$product->name.' ('.$product->category->name.')');
@endphp
<article {{ $attributes->class(['product-card']) }}>
    <a href="{{ $url }}" class="product-card-media" tabindex="-1" aria-hidden="true">
        @if ($img = $product->display_image_url)
            <img src="{{ $img }}" alt="" loading="lazy" width="800" height="500">
        @endif
        <span class="product-card-tag">{{ $product->category->name }}</span>
    </a>
    <div class="product-card-body">
        <h3 class="product-card-title"><a href="{{ $url }}">{{ $product->name }}</a></h3>
        @if ($product->short_description)
            <p class="product-card-text">{{ $product->short_description }}</p>
        @endif
        <div class="product-card-actions">
            <a href="{{ route('contact', ['product' => $product->slug]) }}#enquiry" class="btn btn-maroon"><i class="fa-solid fa-paper-plane me-1"></i> Enquire</a>
            @if ($wa)
                <a href="{{ $wa }}" class="btn btn-whatsapp" target="_blank" rel="noopener" aria-label="Enquire about {{ $product->name }} on WhatsApp"><i class="fa-brands fa-whatsapp"></i></a>
            @endif
            <a href="{{ $url }}" class="btn btn-outline-maroon" aria-label="Details for {{ $product->name }}"><i class="fa-solid fa-arrow-right"></i></a>
        </div>
    </div>
</article>
