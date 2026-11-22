<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\View\View;
use Throwable;

class PasswordResetController extends Controller
{
    public function showForgot(): View
    {
        return view('admin.auth.forgot');
    }

    public function sendLink(Request $request): RedirectResponse
    {
        $request->validate(['email' => ['required', 'email']]);

        try {
            $status = Password::broker('admins')->sendResetLink(
                ['email' => trim((string) $request->input('email')), 'is_active' => true]
            );
        } catch (Throwable $e) {
            Log::error('Admin password reset email failed: '.$e->getMessage());

            return back()->withInput()->withErrors([
                'email' => 'The reset email could not be sent. Please check the SMTP settings or contact the site administrator.',
            ]);
        }

        if ($status === Password::RESET_THROTTLED) {
            return back()->withInput()->withErrors(['email' => 'Please wait a minute before requesting another reset link.']);
        }

        // Same message whether or not the email exists, so accounts cannot be discovered.
        return back()->with('success', 'If an admin account exists for that email, a password reset link has been sent. Please check your inbox (and spam folder).');
    }

    public function showReset(Request $request, string $token): View
    {
        return view('admin.auth.reset', ['token' => $token, 'email' => $request->query('email')]);
    }

    public function reset(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', PasswordRule::min(10)->letters()->mixedCase()->numbers()],
        ]);

        $status = Password::broker('admins')->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (Admin $admin, string $password) {
                $admin->forceFill([
                    'password' => $password, // hashed by the model cast
                    'remember_token' => Str::random(60),
                ])->save();
                event(new PasswordReset($admin));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('admin.login')->with('success', 'Your password has been reset. Please sign in with your new password.');
        }

        return back()->withInput($request->only('email'))->withErrors([
            'email' => $status === Password::INVALID_TOKEN
                ? 'This reset link is invalid or has expired. Please request a new one.'
                : 'We could not reset the password for that email address.',
        ]);
    }
}
