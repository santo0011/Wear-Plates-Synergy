@extends('layouts.admin')

@section('title', 'My Profile')

@section('content')
<div class="row">
    <div class="col-lg-7 col-xl-6">
        <form method="POST" action="{{ route('admin.profile.update') }}" class="card">
            @csrf @method('PUT')
            <div class="card-header"><h2>Profile &amp; Password</h2></div>
            <div class="card-body">
                <x-admin.input name="name" label="Name" :value="$admin->name" :required="true" />
                <x-admin.input name="email" type="email" label="Login Email" :value="$admin->email" :required="true" />
                <hr>
                <p class="help-text">Leave the new password empty to keep your current password. New passwords need at least 10 characters with upper- and lower-case letters and a number.</p>
                <x-admin.input name="password" type="password" label="New Password" autocomplete="new-password" />
                <x-admin.input name="password_confirmation" type="password" label="Confirm New Password" autocomplete="new-password" />
                <hr>
                <x-admin.input name="current_password" type="password" label="Current Password (required to save)" :required="true" autocomplete="current-password" />
                <div class="small text-muted mb-3">Last login: {{ $admin->last_login_at?->format('d M Y, h:i A') ?? '—' }} @if ($admin->last_login_ip) from {{ $admin->last_login_ip }} @endif</div>
                <button class="btn btn-maroon"><i class="fa-solid fa-floppy-disk me-2"></i>Update Profile</button>
            </div>
        </form>
    </div>
</div>
@endsection
