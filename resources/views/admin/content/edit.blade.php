@extends('layouts.admin')

@section('title', $def['label'])

@php
    $pageUrl = \Illuminate\Support\Facades\Route::has($pageDef['route']) ? route($pageDef['route']) : url('/');
    $sectionCount = count($pageDef['sections']);
    $imageFields = collect($def['fields'])->filter(fn ($f) => $f['type'] === 'image' || ($f['type'] === 'repeater' && collect($f['fields'])->contains('type', 'image')));
@endphp

@section('actions')
    <a href="{{ $pageUrl }}#sec-{{ $sectionKey }}" target="_blank" rel="noopener" class="btn btn-outline-maroon d-none d-md-inline-flex align-items-center gap-2">
        <i class="fa-solid fa-eye"></i>Preview Section
    </a>
@endsection

@section('content')
<div class="crumbs">
    <i class="{{ $pageDef['icon'] }}"></i>
    <span>{{ $pageDef['label'] }}</span>
    <i class="fa-solid fa-chevron-right small"></i>
    <strong class="text-dark">{{ $def['label'] }}</strong>
</div>

<nav class="section-tabs" aria-label="{{ $pageDef['label'] }} sections">
    @foreach ($pageDef['extra_links'] ?? [] as [$route, $pattern, $icon, $label])
        <a href="{{ route($route) }}"><i class="{{ $icon }} me-1"></i>{{ $label }}</a>
    @endforeach
    @foreach ($pageDef['sections'] as $key => $sec)
        <a href="{{ route('admin.content.edit', [$page, $key]) }}" @class(['active' => $key === $sectionKey])><i class="{{ $sec['icon'] ?? 'fa-solid fa-pen' }} me-1"></i>{{ $sec['label'] }}</a>
    @endforeach
</nav>

<form method="POST" action="{{ route('admin.content.update', [$page, $sectionKey]) }}" enctype="multipart/form-data" id="contentForm">
    @csrf @method('PUT')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center gap-3 flex-wrap">
            <h2 class="mb-0"><i class="{{ $def['icon'] ?? 'fa-solid fa-pen' }} text-maroon me-2"></i>{{ $def['label'] }}</h2>
            <input type="hidden" name="is_active" value="0">
            <div class="form-check form-switch mb-0">
                <input class="form-check-input" type="checkbox" role="switch" name="is_active" id="is_active" value="1" @checked(old('is_active', $isActive))>
                <label class="form-check-label fw-semibold" for="is_active">Show this section on the website</label>
            </div>
        </div>
        <div class="card-body">
            @if ($imageFields->isNotEmpty())
                <div class="upload-guide mb-4">
                    <i class="fa-solid fa-wand-magic-sparkles me-1"></i>
                    <strong>Images:</strong> JPG, PNG or WebP up to 6 MB. Upload enhanced, high-resolution photos – the website crops them neatly.
                    Until an image is uploaded, a branded placeholder is shown.
                </div>
            @endif

            @foreach ($def['fields'] as $key => $field)
                @if ($field['type'] === 'repeater')
                    @php
                        $oldRows = old("content.$key");
                        if (is_array($oldRows)) {
                            $rows = collect($oldRows)->map(function ($row) use ($field) {
                                foreach ($field['fields'] as $sk => $sf) {
                                    if ($sf['type'] === 'image') { $row[$sk] = $row[$sk.'__current'] ?? null; }
                                }
                                return $row;
                            })->all();
                        } else {
                            $rows = collect($content[$key] ?? [])->values()->mapWithKeys(fn ($row, $i) => ['i'.$i => $row])->all();
                        }
                    @endphp
                    <div class="field-block" data-repeater>
                        <div class="d-flex justify-content-between align-items-end gap-2 mb-2 flex-wrap">
                            <div>
                                <span class="form-label d-block mb-0">{{ $field['label'] }} <span class="badge text-bg-light border ms-1" data-rep-count>{{ count($rows) }}</span></span>
                                @if ($field['help'])<div class="help-text">{{ $field['help'] }}</div>@endif
                            </div>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-sm btn-light border" data-rep-expand>Expand all</button>
                                <button type="button" class="btn btn-sm btn-light border" data-rep-collapse>Collapse all</button>
                            </div>
                        </div>
                        <div class="repeater-items" data-rep-items>
                            @foreach ($rows as $uid => $item)
                                @include('admin.content.repeater-item', ['field' => $field, 'key' => $key, 'uid' => $uid, 'item' => $item, 'index' => $loop->index, 'open' => count($rows) === 1])
                            @endforeach
                        </div>
                        <div class="rep-empty {{ count($rows) ? 'd-none' : '' }}" data-rep-empty>No items yet. Click “Add” to create the first one.</div>
                        <template data-rep-template>
                            @include('admin.content.repeater-item', ['field' => $field, 'key' => $key, 'uid' => '__UID__', 'item' => [], 'index' => null, 'open' => true])
                        </template>
                        <button type="button" class="btn btn-add w-100 mt-2" data-rep-add><i class="fa-solid fa-plus me-2"></i>Add {{ \Illuminate\Support\Str::singular($field['label']) }}</button>
                    </div>
                @else
                    @include('admin.content.field', ['field' => $field, 'key' => $key, 'prefix' => 'content', 'value' => $content[$key] ?? null])
                @endif
            @endforeach
        </div>
        <div class="sticky-save">
            <span class="help-text d-none d-sm-inline"><i class="fa-regular fa-circle-question me-1"></i>Tip: wrap words in **double asterisks** to make them bold / highlighted.</span>
            <button class="btn btn-maroon ms-auto"><i class="fa-solid fa-floppy-disk me-2"></i>Save {{ $def['label'] }}</button>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
(function () {
    var form = document.getElementById('contentForm');

    // ----- Repeaters -----
    function renumber(rep) {
        var items = rep.querySelectorAll('[data-rep-items] > [data-rep-item]');
        items.forEach(function (it, i) { var n = it.querySelector('[data-rep-num]'); if (n) n.textContent = i + 1; });
        rep.querySelector('[data-rep-count]').textContent = items.length;
        rep.querySelector('[data-rep-empty]').classList.toggle('d-none', items.length > 0);
    }
    form.addEventListener('click', function (e) {
        var rep = e.target.closest('[data-repeater]');
        if (!rep) return;
        var item = e.target.closest('[data-rep-item]');
        if (e.target.closest('[data-rep-add]')) {
            var tpl = rep.querySelector('[data-rep-template]').innerHTML.replace(/__UID__/g, 'n' + Date.now() + Math.floor(Math.random() * 1000));
            var wrap = document.createElement('div');
            wrap.innerHTML = tpl.trim();
            var el = wrap.firstElementChild;
            rep.querySelector('[data-rep-items]').appendChild(el);
            renumber(rep);
            bindInside(el);
            var first = el.querySelector('input[type=text], textarea');
            if (first) first.focus();
            return;
        }
        if (e.target.closest('[data-rep-expand]') || e.target.closest('[data-rep-collapse]')) {
            var open = !!e.target.closest('[data-rep-expand]');
            rep.querySelectorAll('[data-rep-item]').forEach(function (it) { it.classList.toggle('is-open', open); });
            return;
        }
        if (!item) return;
        if (e.target.closest('[data-rep-del]')) {
            var label = item.querySelector('[data-rep-title]').textContent.trim();
            if (confirm('Delete "' + label + '"? This is applied when you click Save.')) { item.remove(); renumber(rep); }
            return;
        }
        if (e.target.closest('[data-rep-up]')) { if (item.previousElementSibling) item.parentNode.insertBefore(item, item.previousElementSibling); renumber(rep); return; }
        if (e.target.closest('[data-rep-down]')) { if (item.nextElementSibling) item.parentNode.insertBefore(item.nextElementSibling, item); renumber(rep); return; }
        if (e.target.closest('[data-rep-toggle]')) { item.classList.toggle('is-open'); }
    });
    // Live item title
    form.addEventListener('input', function (e) {
        var item = e.target.closest('[data-rep-item]');
        if (!item) return;
        var title = item.querySelector('[data-rep-title]');
        var key = title.getAttribute('data-title-field');
        if (e.target.name && e.target.name.slice(-(key.length + 2)) === '[' + key + ']') {
            var v = e.target.value.replace(/\*\*/g, '').trim();
            title.textContent = v || 'New item';
            title.classList.toggle('muted', !v);
        }
    });

    // ----- Field helpers (also applied to newly added repeater items) -----
    function autogrow(t) { t.style.height = 'auto'; t.style.height = Math.min(t.scrollHeight + 2, 520) + 'px'; }
    function esc(s) { return s.replace(/[&<>"]/g, function (c) { return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;' }[c]; }); }
    function renderTable(src) {
        var box = document.getElementById(src.getAttribute('data-table-source'));
        var rows = src.value.split(/\r?\n/).map(function (l) { return l.trim(); }).filter(Boolean).map(function (l) { return l.split('|').map(function (c) { return c.trim(); }); });
        if (!rows.length) { box.innerHTML = ''; box.style.display = 'none'; return; }
        box.style.display = '';
        var html = '<table class="table table-sm table-bordered"><thead><tr>' + rows[0].map(function (c) { return '<th>' + esc(c) + '</th>'; }).join('') + '</tr></thead><tbody>';
        rows.slice(1).forEach(function (r) { html += '<tr>' + r.map(function (c) { return '<td>' + esc(c) + '</td>'; }).join('') + '</tr>'; });
        box.innerHTML = html + '</tbody></table>';
    }
    function bindInside(root) {
        root.querySelectorAll('[data-autogrow]').forEach(function (t) { t.addEventListener('input', function () { autogrow(t); }); });
        root.querySelectorAll('[data-table-source]').forEach(function (t) { renderTable(t); t.addEventListener('input', function () { renderTable(t); }); });
        root.querySelectorAll('[data-icon-input]').forEach(function (inp) {
            inp.addEventListener('input', function () { var i = inp.parentNode.querySelector('.icon-preview i'); i.className = inp.value.trim() || 'fa-solid fa-question'; });
        });
        root.querySelectorAll('[data-img-input]').forEach(function (inp) {
            inp.addEventListener('change', function () {
                var id = inp.getAttribute('data-img-input');
                var img = document.getElementById(id + '_preview'), empty = document.getElementById(id + '_empty');
                if (inp.files && inp.files[0]) {
                    if (inp.files[0].size > 6 * 1024 * 1024) { alert('This image is larger than 6 MB. Please compress it and try again.'); inp.value = ''; return; }
                    img.src = URL.createObjectURL(inp.files[0]); img.classList.remove('d-none'); if (empty) empty.classList.add('d-none');
                }
            });
        });
    }
    bindInside(form);
    // Size textareas once visible (collapsed repeater bodies have no height yet).
    form.querySelectorAll('[data-autogrow]').forEach(autogrow);
    form.addEventListener('click', function (e) { if (e.target.closest('[data-rep-toggle], [data-rep-expand]')) setTimeout(function () { form.querySelectorAll('.rep-item.is-open [data-autogrow]').forEach(autogrow); }, 0); });

    // Warn about unsaved changes
    var dirty = false;
    form.addEventListener('input', function () { dirty = true; });
    form.addEventListener('change', function () { dirty = true; });
    form.addEventListener('submit', function () { dirty = false; var b = form.querySelector('.sticky-save .btn'); b.disabled = true; b.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Saving…'; });
    window.addEventListener('beforeunload', function (e) { if (dirty) { e.preventDefault(); e.returnValue = ''; } });
})();
</script>
@endpush
