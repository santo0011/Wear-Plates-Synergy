@props(['limit' => null])
@php
    $items = setting_list('why_items');
    if ($limit) { $items = array_slice($items, 0, $limit); }
@endphp
<div class="row g-4">
    @foreach ($items as $i => $item)
        @php
            // Line format: icon | title | description (icon optional)
            if (count($item) >= 3) { [$icon, $title, $text] = $item; }
            elseif (count($item) === 2) { [$title, $text] = $item; $icon = 'fa-solid fa-check'; }
            else { $title = $item[0]; $text = ''; $icon = 'fa-solid fa-check'; }
            $icon = preg_match('/^[a-z0-9\- ]+$/', $icon) ? $icon : 'fa-solid fa-check';
        @endphp
        <div class="col-md-6 col-lg-4">
            <div class="why-card reveal" data-delay="{{ ($i % 3) + 1 }}">
                <span class="why-num">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>
                <div class="why-icon"><i class="{{ $icon }}"></i></div>
                <h3>{{ $title }}</h3>
                @if ($text)<p>{{ $text }}</p>@endif
            </div>
        </div>
    @endforeach
</div>
