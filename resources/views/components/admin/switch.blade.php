@props(['name', 'label', 'checked' => false, 'help' => null])
@php $id = 'f_'.str_replace(['[', ']', '.'], '_', $name); $dot = str_replace(['[', ']'], ['.', ''], $name); @endphp
<div class="mb-3">
    <input type="hidden" name="{{ $name }}" value="0">
    <div class="form-check form-switch">
        <input class="form-check-input" type="checkbox" role="switch" name="{{ $name }}" id="{{ $id }}" value="1" @checked(old($dot, $checked))>
        <label class="form-check-label fw-semibold" for="{{ $id }}">{{ $label }}</label>
    </div>
    @if ($help)<div class="help-text mt-1">{{ $help }}</div>@endif
</div>
