{{-- One repeater item. Params: $field, $key, $uid, $item, $index, $open --}}
@php
    $prefix = 'content['.$key.']['.$uid.']';
    $titleKey = $field['title'] ?? array_key_first($field['fields']);
    $title = trim((string) ($item[$titleKey] ?? ''));
    $title = $title !== '' ? \Illuminate\Support\Str::limit(str_replace('**', '', $title), 80) : null;
    $thumb = collect($field['fields'])->filter(fn ($f) => $f['type'] === 'image')->keys()->map(fn ($k) => $item[$k] ?? null)->filter()->first();
    $imageKeys = collect($field['fields'])->filter(fn ($f) => $f['type'] === 'image')->keys();
    $missingImage = $imageKeys->isNotEmpty() && ! $thumb;
@endphp
<div @class(['rep-item', 'is-open' => $open ?? false]) data-rep-item>
    <div class="rep-head" data-rep-toggle>
        <span class="rep-num" data-rep-num>{{ is_int($index) ? $index + 1 : '+' }}</span>
        @if ($thumb)<img src="{{ asset($thumb) }}" alt="" class="rep-thumb">@endif
        <span @class(['rep-title', 'muted' => ! $title]) data-rep-title data-title-field="{{ $titleKey }}">{{ $title ?? 'New item' }}</span>
        @if ($missingImage)<span class="rep-flag d-none d-md-inline"><i class="fa-regular fa-image me-1"></i>Image pending</span>@endif
        <span class="rep-actions" onclick="event.stopPropagation()">
            <button type="button" data-rep-up title="Move up" aria-label="Move up"><i class="fa-solid fa-arrow-up"></i></button>
            <button type="button" data-rep-down title="Move down" aria-label="Move down"><i class="fa-solid fa-arrow-down"></i></button>
            <button type="button" class="rep-del" data-rep-del title="Delete" aria-label="Delete item"><i class="fa-regular fa-trash-can"></i></button>
        </span>
        <i class="fa-solid fa-chevron-down rep-caret" aria-hidden="true"></i>
    </div>
    <div class="rep-body">
        @foreach ($field['fields'] as $subKey => $sub)
            @include('admin.content.field', ['field' => $sub, 'key' => $subKey, 'prefix' => $prefix, 'value' => $item[$subKey] ?? null])
        @endforeach
    </div>
</div>
