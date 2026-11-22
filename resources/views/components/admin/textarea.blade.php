@props(['name', 'label', 'value' => null, 'help' => null, 'required' => false, 'rows' => 4, 'id' => null])
@php $id = $id ?? 'f_'.str_replace(['[', ']', '.'], '_', $name); $dot = str_replace(['[', ']'], ['.', ''], $name); @endphp
<div class="mb-3">
    <label for="{{ $id }}" class="form-label">{{ $label }} @if ($required)<span class="text-danger">*</span>@endif</label>
    <textarea name="{{ $name }}" id="{{ $id }}" rows="{{ $rows }}"
        {{ $attributes->class(['form-control', 'is-invalid' => $errors->has($dot)]) }} @required($required)>{{ old($dot, $value) }}</textarea>
    @error($dot)<div class="invalid-feedback">{{ $message }}</div>@enderror
    @if ($help)<div class="help-text mt-1">{{ $help }}</div>@endif
</div>
