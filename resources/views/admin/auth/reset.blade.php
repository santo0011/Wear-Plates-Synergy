@extends('admin.auth.layout')

@section('title', 'Reset Password')

@section('form')
    <span class="auth-badge"><i class="fa-solid fa-lock-open"></i> Set New Password</span>
    <h2 class="auth-title">Reset password</h2>
    <p class="auth-sub">Choose a strong new password for your admin account.</p>

    @if ($errors->any())
        <div class="auth-alert err" role="alert"><i class="fa-solid fa-circle-exclamation"></i><span>{{ $errors->first() }}</span></div>
    @endif

    <form method="POST" action="{{ route('admin.password.update') }}" data-auth-form novalidate>
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <div class="auth-field">
            <label for="email">Email address</label>
            <div class="auth-input @error('email') is-invalid @enderror">
                <i class="fa-regular fa-envelope"></i>
                <input type="email" id="email" name="email" value="{{ old('email', $email) }}" required autocomplete="username">
            </div>
        </div>
        <div class="auth-field">
            <label for="password">New password</label>
            <div class="auth-input @error('password') is-invalid @enderror">
                <i class="fa-solid fa-lock"></i>
                <input type="password" id="password" name="password" placeholder="At least 10 characters" required minlength="10" autocomplete="new-password" autofocus>
                <button type="button" class="auth-eye" data-toggle-password="password" aria-label="Show password" title="Show password"><i class="fa-regular fa-eye"></i></button>
            </div>
            <small class="auth-caps" data-caps-for="password" hidden><i class="fa-solid fa-triangle-exclamation me-1"></i>Caps Lock is on</small>
            <ul class="auth-rules" id="pwRules">
                <li data-rule="len">At least 10 characters</li>
                <li data-rule="upper">An uppercase letter</li>
                <li data-rule="lower">A lowercase letter</li>
                <li data-rule="num">A number</li>
            </ul>
        </div>
        <div class="auth-field">
            <label for="password_confirmation">Confirm new password</label>
            <div class="auth-input">
                <i class="fa-solid fa-lock"></i>
                <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Repeat the new password" required autocomplete="new-password">
                <button type="button" class="auth-eye" data-toggle-password="password_confirmation" aria-label="Show password" title="Show password"><i class="fa-regular fa-eye"></i></button>
            </div>
            <small class="auth-match" id="pwMatch" hidden></small>
        </div>
        <button type="submit" class="auth-submit mt-2" data-loading="Saving…">
            <span class="lbl">Reset Password</span><i class="fa-solid fa-check"></i>
        </button>
    </form>

    <a href="{{ route('admin.login') }}" class="auth-back"><i class="fa-solid fa-arrow-left"></i> Back to sign in</a>
@endsection

@push('scripts')
<script>
    (function () {
        var pw = document.getElementById('password'), cf = document.getElementById('password_confirmation');
        var rules = { len: function (v) { return v.length >= 10; }, upper: /[A-Z]/, lower: /[a-z]/, num: /[0-9]/ };
        var match = document.getElementById('pwMatch');
        function check() {
            var v = pw.value;
            document.querySelectorAll('#pwRules li').forEach(function (li) {
                var r = rules[li.getAttribute('data-rule')];
                li.classList.toggle('ok', typeof r === 'function' ? r(v) : r.test(v));
            });
            if (cf.value) {
                var same = cf.value === v;
                match.hidden = false;
                match.className = 'auth-match ' + (same ? 'ok' : 'bad');
                match.textContent = same ? 'Passwords match' : 'Passwords do not match';
            } else { match.hidden = true; }
        }
        pw.addEventListener('input', check); cf.addEventListener('input', check);
    })();
</script>
@endpush
