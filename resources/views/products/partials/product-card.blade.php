@php $photo = $product->image; @endphp
<a href="{{ $product->url }}" class="prod-card">
    <x-media :src="$photo" :alt="$product->name" :icon="$product->category?->icon_class ?? 'fa-solid fa-box'" class="prod-img" />
    <span class="prod-body">
        @if ($product->is_featured)<span class="tag tag-gold">Featured</span>@endif
        <strong class="prod-name">{{ $product->name }}</strong>
        @if ($product->subtitle)<span class="prod-sub">{{ $product->subtitle }}</span>@endif
        <span class="prod-text">{{ \Illuminate\Support\Str::limit($product->short_description, 120) }}</span>
        <span class="more">View details <i class="fa-solid fa-arrow-right"></i></span>
    </span>
</a>
