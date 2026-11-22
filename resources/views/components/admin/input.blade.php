@props(['name', 'label', 'type' => 'text', 'value' => null, 'help' => null, 'required' => false, 'id' => null])
@php $id = $id ?? 'f_'.str_replace(['[', ']', '.'], '_', $name); $dot = str_replace(['[', ']'], ['.', ''], $name); @endphp
<div class="mb-3">
    <label for="{{ $id }}" class="form-label">{{ $label }} @if ($required)<span class="text-danger">*</span>@endif</label>
    <input type="{{ $type }}" name="{{ $name }}" id="{{ $id }}" value="{{ old($dot, $value) }}"
        {{ $attributes->class(['form-control', 'is-invalid' => $errors->has($dot)]) }} @required($required)>
    @error($dot)<div class="invalid-feedback">{{ $message }}</div>@enderror
    @if ($help)<div class="help-text mt-1">{{ $help }}</div>@endif
</div>
