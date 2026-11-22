@props(['category', 'index' => null, 'showProducts' => false])
<a href="{{ route('products.category', $category) }}" {{ $attributes->class(['cat-card']) }}>
    <div class="cat-card-media">
        @if ($index !== null)
            <span class="cat-card-num">{{ str_pad($index, 2, '0', STR_PAD_LEFT) }}</span>
        @endif
        @if ($category->image_url)
            <img src="{{ $category->image_url }}" alt="{{ $category->name }}" loading="lazy" width="800" height="560">
        @endif
        <span class="cat-card-icon"><i class="{{ $category->icon_class }}"></i></span>
    </div>
    <div class="cat-card-body">
        <h3 class="cat-card-title">{{ $category->name }}</h3>
        @if ($category->short_description)
            <p class="cat-card-text">{{ $category->short_description }}</p>
        @endif
        @if ($showProducts && $category->relationLoaded('activeProducts') && $category->activeProducts->isNotEmpty())
            <ul class="cat-card-products">
                @foreach ($category->activeProducts as $p)
                    <li>{{ $p->name }}</li>
                @endforeach
            </ul>
        @endif
        <span class="cat-card-link">View Solutions <i class="fa-solid fa-arrow-right-long"></i></span>
    </div>
</a>
