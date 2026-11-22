{{--
    One CMS field. Params: $field (normalised definition), $key, $prefix (input name prefix, e.g. "content" or
    "content[slides][i0]"), $value (current value).
--}}
@php
    $name = $prefix.'['.$key.']';
    $dot = str_replace(['[', ']'], ['.', ''], $name);
    $id = 'f_'.preg_replace('/[^A-Za-z0-9_]/', '_', $name);
    $type = $field['type'];
    $value = old($dot, $value);
    $typeLabel = ['lines' => 'one per line', 'pairs' => 'Label | Value', 'table' => 'table', 'image' => 'image', 'icon' => 'icon'][$type] ?? null;
@endphp
<div class="field-block">
    @if ($type === 'boolean')
        <input type="hidden" name="{{ $name }}" value="0">
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" role="switch" name="{{ $name }}" id="{{ $id }}" value="1" @checked($value)>
            <label class="form-check-label fw-semibold" for="{{ $id }}">{{ $field['label'] }}</label>
        </div>
    @else
        <label for="{{ $id }}" class="form-label">{{ $field['label'] }}@if ($typeLabel)<span class="field-type">{{ $typeLabel }}</span>@endif</label>

        @switch($type)
            @case('textarea')
                <textarea name="{{ $name }}" id="{{ $id }}" rows="{{ max(3, min(14, substr_count((string) $value, "\n") + 2)) }}" class="form-control @error($dot) is-invalid @enderror" data-autogrow>{{ $value }}</textarea>
                @break

            @case('lines')
            @case('pairs')
                <textarea name="{{ $name }}" id="{{ $id }}" rows="{{ max(4, min(16, substr_count((string) $value, "\n") + 2)) }}" class="form-control @error($dot) is-invalid @enderror {{ $type === 'pairs' ? 'mono' : '' }}" data-autogrow data-line-count>{{ $value }}</textarea>
                @break

            @case('table')
                <textarea name="{{ $name }}" id="{{ $id }}" rows="{{ max(6, min(20, substr_count((string) $value, "\n") + 2)) }}" class="form-control mono @error($dot) is-invalid @enderror" data-autogrow data-table-source="{{ $id }}_preview" spellcheck="false">{{ $value }}</textarea>
                <div class="table-preview" id="{{ $id }}_preview" aria-label="Table preview"></div>
                @break

            @case('icon')
                <div class="d-flex gap-2 align-items-center">
                    <span class="icon-preview"><i class="{{ $value ?: 'fa-solid fa-question' }}" data-icon-preview="{{ $id }}"></i></span>
                    <input type="text" name="{{ $name }}" id="{{ $id }}" value="{{ $value }}" class="form-control mono @error($dot) is-invalid @enderror" placeholder="fa-solid fa-industry" data-icon-input>
                </div>
                @break

            @case('image')
                @php $current = old(str_replace(['[', ']'], ['.', ''], $prefix.'['.$key.'__current]'), $value); @endphp
                <div class="img-field">
                    <div class="img-box">
                        <img id="{{ $id }}_preview" src="{{ $current ? asset($current) : '' }}" alt="" class="{{ $current ? '' : 'd-none' }}">
                        <span class="empty {{ $current ? 'd-none' : '' }}" id="{{ $id }}_empty"><i class="fa-regular fa-image"></i>No image yet</span>
                    </div>
                    <div class="img-controls">
                        <input type="hidden" name="{{ $prefix }}[{{ $key }}__current]" value="{{ $current }}">
                        <input type="file" name="{{ $name }}" id="{{ $id }}" accept="image/jpeg,image/png,image/webp"
                               class="form-control @error($dot) is-invalid @enderror" data-img-input="{{ $id }}">
                        @error($dot)<div class="invalid-feedback">{{ $message }}</div>@enderror
                        @if ($current)
                            <div class="form-check mt-2">
                                <input class="form-check-input" type="checkbox" name="{{ $prefix }}[{{ $key }}__remove]" value="1" id="{{ $id }}_remove">
                                <label class="form-check-label small" for="{{ $id }}_remove">Remove this image</label>
                            </div>
                        @endif
                    </div>
                </div>
                @break

            @default
                <input type="text" name="{{ $name }}" id="{{ $id }}" value="{{ $value }}" class="form-control @error($dot) is-invalid @enderror">
        @endswitch

        @if ($type !== 'image')
            @error($dot)<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        @endif
    @endif
    @if ($field['help'])<div class="help-text mt-1">{{ $field['help'] }}</div>@endif
</div>
