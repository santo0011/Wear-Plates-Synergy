@extends('admin.auth.layout')

@section('title', 'Admin Login')

@section('form')
    <span class="auth-badge"><i class="fa-solid fa-shield-halved"></i> Secure Admin Area</span>
    <h2 class="auth-title">Welcome back</h2>
    <p class="auth-sub">Sign in to manage products, enquiries and website content.</p>

    @if ($errors->any())
        <div class="auth-alert err" role="alert"><i class="fa-solid fa-circle-exclamation"></i><span>{{ $errors->first() }}</span></div>
    @endif

    <form method="POST" action="{{ route('admin.login.attempt') }}" data-auth-form novalidate>
        @csrf
        <div class="auth-field">
            <label for="email">Email address</label>
            <div class="auth-input @error('email') is-invalid @enderror">
                <i class="fa-regular fa-envelope"></i>
                <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="you@company.com" required autofocus autocomplete="username">
            </div>
        </div>
        <div class="auth-field">
            <div class="d-flex justify-content-between align-items-baseline">
                <label for="password">Password</label>
                <a href="{{ route('admin.password.request') }}" class="auth-link small">Forgot password?</a>
            </div>
            <div class="auth-input @error('password') is-invalid @enderror">
                <i class="fa-solid fa-lock"></i>
                <input type="password" id="password" name="password" placeholder="Enter your password" required autocomplete="current-password">
                <button type="button" class="auth-eye" data-toggle-password="password" aria-label="Show password" title="Show password"><i class="fa-regular fa-eye"></i></button>
            </div>
            <small class="auth-caps" data-caps-for="password" hidden><i class="fa-solid fa-triangle-exclamation me-1"></i>Caps Lock is on</small>
        </div>
        <div class="auth-row">
            <label class="auth-check">
                <input type="checkbox" name="remember" value="1" @checked(old('remember'))>
                <span></span>Keep me signed in
            </label>
        </div>
        <button type="submit" class="auth-submit" data-loading="Signing in…">
            <span class="lbl">Sign In</span><i class="fa-solid fa-arrow-right-to-bracket"></i>
        </button>
    </form>

    <a href="{{ route('home') }}" class="auth-back"><i class="fa-solid fa-arrow-left"></i> Back to website</a>
@endsection
