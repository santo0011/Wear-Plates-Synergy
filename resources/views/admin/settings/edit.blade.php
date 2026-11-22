@extends('layouts.admin')

@section('title', $groups[$group]['label'])

@section('content')
<ul class="nav nav-pills flex-wrap mb-4 gap-1">
    @foreach ($groups as $key => $g)
        <li class="nav-item"><a class="nav-link text-nowrap {{ $key === $group ? 'active bg-maroon' : 'text-maroon' }}" style="{{ $key === $group ? 'background:#6b1424' : '' }}" href="{{ route('admin.settings.edit', $key) }}"><i class="{{ $g['icon'] }} me-1"></i>{{ $g['label'] }}</a></li>
    @endforeach
</ul>

<form method="POST" action="{{ route('admin.settings.update', $group) }}" enctype="multipart/form-data">
    @csrf @method('PUT')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h2><i class="{{ $groups[$group]['icon'] }} text-maroon me-2"></i>{{ $groups[$group]['label'] }}</h2>
            <button class="btn btn-maroon"><i class="fa-solid fa-floppy-disk me-2"></i>Save</button>
        </div>
        <div class="card-body">
            @if ($group === 'contact' || $group === 'about')
                <div class="alert alert-info small"><i class="fa-solid fa-circle-info me-1"></i>Only publish verified details. Empty fields are hidden on the website.</div>
            @endif
            @if ($group === 'mail')
                @php $smtpOn = \App\Support\MailSettings::isConfigured(); @endphp
                <div class="alert {{ $smtpOn ? 'alert-success' : 'alert-warning' }} small d-flex gap-2">
                    <i class="fa-solid {{ $smtpOn ? 'fa-circle-check' : 'fa-triangle-exclamation' }} mt-1"></i>
                    <div>
                        @if ($smtpOn)
                            <strong>SMTP is active.</strong> Password reset emails and enquiry alerts are sent through <strong>{{ setting('smtp_host') }}</strong>. Use “Send test email” below to check the connection.
                        @else
                            <strong>Email is not set up.</strong> Fill in your SMTP details so the website can send password reset emails and enquiry alerts. Get these from your email/hosting provider (for Gmail: host <code>smtp.gmail.com</code>, port 587, TLS, and an App Password).
                        @endif
                    </div>
                </div>
            @endif
            <div class="row">
                @foreach ($settings as $s)
                    @php $field = "settings[{$s->key}]"; @endphp
                    <div class="{{ in_array($s->type, ['textarea', 'list', 'image', 'embed']) ? 'col-12' : 'col-md-6' }}">
                        @switch($s->type)
                            @case('textarea')
                                <x-admin.textarea :name="$field" :label="$s->label" :value="$s->value" :help="$s->help" rows="4" />
                                @break
                            @case('list')
                                <x-admin.textarea :name="$field" :label="$s->label" :value="$s->value" :help="$s->help" rows="8" class="font-monospace small" />
                                @break
                            @case('image')
                                <x-admin.image :name="$field" :label="$s->label" :current="$s->value" :remove-name="'remove['.$s->key.']'" :help="$s->help ?: 'JPG, PNG or WebP. Max 3 MB.'" />
                                @break
                            @case('boolean')
                                <x-admin.switch :name="$field" :label="$s->label" :checked="(bool) $s->value" :help="$s->help" />
                                @break
                            @case('embed')
                                <x-admin.input :name="$field" :label="$s->label" type="url" :value="$s->value" :help="$s->help" placeholder="https://www.google.com/maps/embed?pb=..." />
                                @break
                            @case('select')
                                @php $id = 'f_settings_'.$s->key; @endphp
                                <div class="mb-3">
                                    <label for="{{ $id }}" class="form-label">{{ $s->label }}</label>
                                    <select name="{{ $field }}" id="{{ $id }}" class="form-select @error('settings.'.$s->key) is-invalid @enderror">
                                        @foreach (\App\Models\Setting::OPTIONS[$s->key] ?? [] as $optValue => $optLabel)
                                            <option value="{{ $optValue }}" @selected(old('settings.'.$s->key, $s->value) === (string) $optValue)>{{ $optLabel }}</option>
                                        @endforeach
                                    </select>
                                    @error('settings.'.$s->key)<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    @if ($s->help)<div class="help-text mt-1">{{ $s->help }}</div>@endif
                                </div>
                                @break
                            @case('password')
                                @php $id = 'f_settings_'.$s->key; @endphp
                                <div class="mb-3">
                                    <label for="{{ $id }}" class="form-label">{{ $s->label }}
                                        @if ($s->value)<span class="badge text-bg-success ms-1"><i class="fa-solid fa-lock me-1"></i>Saved</span>@endif
                                    </label>
                                    <div class="input-group">
                                        <input type="password" name="{{ $field }}" id="{{ $id }}" class="form-control" autocomplete="new-password"
                                               placeholder="{{ $s->value ? '•••••••• (leave blank to keep)' : 'Enter password' }}">
                                        <button type="button" class="btn btn-light border" onclick="var i=document.getElementById('{{ $id }}');i.type=i.type==='password'?'text':'password';this.innerHTML=i.type==='password'?'<i class=\'fa-regular fa-eye\'></i>':'<i class=\'fa-regular fa-eye-slash\'></i>'" aria-label="Show password"><i class="fa-regular fa-eye"></i></button>
                                    </div>
                                    @if ($s->help)<div class="help-text mt-1">{{ $s->help }}</div>@endif
                                    @if ($s->value)
                                        <div class="form-check mt-1">
                                            <input class="form-check-input" type="checkbox" name="clear[{{ $s->key }}]" value="1" id="{{ $id }}_clear">
                                            <label class="form-check-label small" for="{{ $id }}_clear">Remove saved password</label>
                                        </div>
                                    @endif
                                </div>
                                @break
                            @default
                                <x-admin.input :name="$field" :label="$s->label" :type="in_array($s->type, ['email', 'url', 'tel']) ? $s->type : 'text'" :value="$s->value" :help="$s->help" />
                        @endswitch
                    </div>
                @endforeach
            </div>
        </div>
        <div class="card-footer bg-white text-end">
            <button class="btn btn-maroon"><i class="fa-solid fa-floppy-disk me-2"></i>Save {{ $groups[$group]['label'] }}</button>
        </div>
    </div>
</form>

@if ($group === 'mail')
    <form method="POST" action="{{ route('admin.settings.mail.test') }}" class="card mt-4">
        @csrf
        <div class="card-header"><h2><i class="fa-solid fa-paper-plane text-maroon me-2"></i>Send Test Email</h2></div>
        <div class="card-body">
            <p class="help-text mb-3">Save your SMTP settings first, then send a test email to confirm everything works.</p>
            <div class="row g-2 align-items-start">
                <div class="col-md-8">
                    <input type="email" name="test_email" class="form-control @error('test_email') is-invalid @enderror"
                           value="{{ old('test_email', auth('admin')->user()->email) }}" placeholder="Send the test to…" required>
                    @error('test_email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4 d-grid">
                    <button class="btn btn-outline-maroon"><i class="fa-solid fa-paper-plane me-2"></i>Send Test Email</button>
                </div>
            </div>
        </div>
    </form>
@endif
@endsection
