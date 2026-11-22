@props(['name', 'label', 'current' => null, 'removeName' => 'remove_image', 'help' => 'JPG, PNG or WebP. Max 3 MB.'])
@php $id = 'f_'.str_replace(['[', ']', '.'], '_', $name); $dot = str_replace(['[', ']'], ['.', ''], $name); @endphp
<div class="mb-3">
    <label for="{{ $id }}" class="form-label">{{ $label }}</label>
    <div class="mb-2">
        <img id="{{ $id }}_preview" src="{{ $current ? asset($current) : '' }}" alt="Preview" class="img-preview {{ $current ? '' : 'd-none' }}">
    </div>
    <input type="file" name="{{ $name }}" id="{{ $id }}" accept="image/jpeg,image/png,image/webp" data-preview="{{ $id }}_preview"
        class="form-control @error($dot) is-invalid @enderror">
    @error($dot)<div class="invalid-feedback">{{ $message }}</div>@enderror
    <div class="help-text mt-1">{{ $help }}</div>
    @if ($current)
        <div class="form-check mt-2">
            <input class="form-check-input" type="checkbox" name="{{ $removeName }}" value="1" id="{{ $id }}_remove">
            <label class="form-check-label small" for="{{ $id }}_remove">Remove current image</label>
        </div>
    @endif
</div>
