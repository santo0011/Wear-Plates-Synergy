@extends('admin.auth.layout')

@section('title', 'Forgot Password')

@section('form')
    <span class="auth-badge"><i class="fa-solid fa-key"></i> Password Recovery</span>
    <h2 class="auth-title">Forgot password?</h2>
    <p class="auth-sub">Enter your admin email address and we'll send you a link to set a new password.</p>

    @if ($errors->any())
        <div class="auth-alert err" role="alert"><i class="fa-solid fa-circle-exclamation"></i><span>{{ $errors->first() }}</span></div>
    @endif

    <form method="POST" action="{{ route('admin.password.email') }}" data-auth-form novalidate>
        @csrf
        <div class="auth-field">
            <label for="email">Email address</label>
            <div class="auth-input @error('email') is-invalid @enderror">
                <i class="fa-regular fa-envelope"></i>
                <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="you@company.com" required autofocus autocomplete="username">
            </div>
        </div>
        <button type="submit" class="auth-submit mt-2" data-loading="Sending link…">
            <span class="lbl">Send Reset Link</span><i class="fa-regular fa-paper-plane"></i>
        </button>
    </form>

    <a href="{{ route('admin.login') }}" class="auth-back"><i class="fa-solid fa-arrow-left"></i> Back to sign in</a>
@endsection
