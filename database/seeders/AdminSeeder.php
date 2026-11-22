<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * Creates the first admin account from ADMIN_NAME / ADMIN_EMAIL / ADMIN_PASSWORD in .env.
 * - Never overwrites an existing admin's password.
 * - Refuses to create an account in production without an explicit strong password.
 * - Outside production, generates a random password (printed once) when none is set.
 */
class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $email = config('admin.email');
        $password = config('admin.password');

        if (blank($email)) {
            $this->command?->warn('ADMIN_EMAIL is not set in .env – skipping admin account creation.');

            return;
        }

        if (Admin::where('email', $email)->exists()) {
            $this->command?->info("Admin account {$email} already exists – password left unchanged.");

            return;
        }

        if (blank($password)) {
            if (app()->environment('production')) {
                throw new RuntimeException('Set ADMIN_PASSWORD in .env before seeding in production.');
            }
            $password = Str::password(16, symbols: false);
            $this->command?->warn("Generated admin password (shown once): {$password}");
        } elseif (app()->environment('production') && strlen($password) < 12) {
            throw new RuntimeException('ADMIN_PASSWORD must be at least 12 characters in production.');
        }

        Admin::create([
            'name' => config('admin.name'),
            'email' => $email,
            'password' => Hash::make($password),
            'is_active' => true,
        ]);

        $this->command?->info("Admin account created: {$email}");
    }
}
