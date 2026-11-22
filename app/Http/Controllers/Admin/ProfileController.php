<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(): View
    {
        return view('admin.profile', ['admin' => Auth::guard('admin')->user()]);
    }

    public function update(Request $request): RedirectResponse
    {
        $admin = Auth::guard('admin')->user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('admins', 'email')->ignore($admin->id)],
            'current_password' => ['required', 'current_password:admin'],
            'password' => ['nullable', 'confirmed', Password::min(10)->letters()->mixedCase()->numbers()],
        ]);

        $admin->name = $data['name'];
        $admin->email = $data['email'];
        if (! empty($data['password'])) {
            $admin->password = $data['password']; // hashed by the model cast
        }
        $admin->save();

        if (! empty($data['password'])) {
            // Sign out any other sessions using the old password.
            Auth::guard('admin')->logoutOtherDevices($data['password']);
        }

        return back()->with('success', 'Profile updated.');
    }
}
