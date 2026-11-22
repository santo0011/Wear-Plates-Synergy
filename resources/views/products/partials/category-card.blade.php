{{-- Category card. Bundled SVG artwork (images/categories) is treated as "no photo" so a clean placeholder shows. --}}
@php $photo = $category->image && str_starts_with($category->image, 'uploads/') ? $category->image : null; @endphp
<a href="{{ route('products.category', $category) }}" class="cat-card">
    <x-media :src="$photo" :alt="$category->name" :icon="$category->icon_class" class="cat-img" />
    <span class="cat-body">
        <span class="cat-icon"><i class="{{ $category->icon_class }}" aria-hidden="true"></i></span>
        <strong class="cat-name">{{ $category->name }}</strong>
        <span class="cat-text">{{ \Illuminate\Support\Str::limit($category->short_description, 130) }}</span>
        <span class="cat-foot">
            @isset($category->products_count)<span>{{ $category->products_count }} {{ \Illuminate\Support\Str::plural('product', $category->products_count) }}</span>@endisset
            <span class="more">Explore <i class="fa-solid fa-arrow-right"></i></span>
        </span>
    </span>
</a>
