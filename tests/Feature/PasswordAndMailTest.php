<?php

namespace Tests\Feature;

use App\Mail\NewEnquiryMail;
use App\Models\Admin;
use App\Models\Setting;
use App\Notifications\AdminResetPassword;
use App\Support\MailSettings;
use Database\Seeders\CatalogueSeeder;
use Database\Seeders\LocationSeeder;
use Database\Seeders\SettingSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class PasswordAndMailTest extends TestCase
{
    use RefreshDatabase;

    private Admin $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([SettingSeeder::class, CatalogueSeeder::class, LocationSeeder::class]);
        $this->admin = Admin::create(['name' => 'Admin', 'email' => 'admin@test.local', 'password' => 'Secret-Pass-123']);
    }

    public function test_auth_pages_render(): void
    {
        $this->get(route('admin.login'))->assertOk()->assertSee('Forgot password?');
        $this->get(route('admin.password.request'))->assertOk()->assertSee('Send Reset Link');
        $this->get(route('admin.password.reset', ['token' => 'abc', 'email' => 'admin@test.local']))->assertOk()->assertSee('Reset Password');
    }

    public function test_reset_link_is_sent_to_existing_admin(): void
    {
        Notification::fake();

        $this->post(route('admin.password.email'), ['email' => 'admin@test.local'])
            ->assertSessionHas('success');

        Notification::assertSentTo($this->admin, AdminResetPassword::class, function ($n) {
            $mail = $n->toMail($this->admin);

            return str_contains($mail->actionUrl, '/admin/reset-password/') && str_contains($mail->actionUrl, 'email=');
        });
    }

    public function test_unknown_email_gets_the_same_generic_message(): void
    {
        Notification::fake();

        $this->post(route('admin.password.email'), ['email' => 'nobody@test.local'])
            ->assertSessionHas('success')
            ->assertSessionHasNoErrors();

        Notification::assertNothingSent();
    }

    public function test_password_can_be_reset_with_valid_token(): void
    {
        $token = Password::broker('admins')->createToken($this->admin);

        $this->post(route('admin.password.update'), [
            'token' => $token, 'email' => 'admin@test.local',
            'password' => 'Brand-New-Pass-9', 'password_confirmation' => 'Brand-New-Pass-9',
        ])->assertRedirect(route('admin.login'))->assertSessionHas('success');

        $this->assertTrue(password_verify('Brand-New-Pass-9', $this->admin->fresh()->getRawOriginal('password')));

        $this->post(route('admin.login.attempt'), ['email' => 'admin@test.local', 'password' => 'Brand-New-Pass-9'])
            ->assertRedirect(route('admin.dashboard'));
    }

    public function test_invalid_token_and_weak_password_are_rejected(): void
    {
        $this->post(route('admin.password.update'), [
            'token' => 'wrong-token', 'email' => 'admin@test.local',
            'password' => 'Brand-New-Pass-9', 'password_confirmation' => 'Brand-New-Pass-9',
        ])->assertSessionHasErrors('email');

        $token = Password::broker('admins')->createToken($this->admin);
        $this->post(route('admin.password.update'), [
            'token' => $token, 'email' => 'admin@test.local', 'password' => '123456', 'password_confirmation' => '123456',
        ])->assertSessionHasErrors('password');

        $this->assertTrue(password_verify('Secret-Pass-123', $this->admin->fresh()->getRawOriginal('password')));
    }

    public function test_smtp_settings_are_saved_encrypted_and_applied(): void
    {
        $this->actingAs($this->admin, 'admin');
        $this->get(route('admin.settings.edit', 'mail'))->assertOk()->assertSee('SMTP Host')->assertSee('Send Test Email');

        $payload = ['settings' => [
            'smtp_host' => 'smtp.example.com', 'smtp_port' => '465', 'smtp_encryption' => 'ssl',
            'smtp_username' => 'mailer@example.com', 'smtp_password' => 'app-secret-123',
            'mail_from_address' => 'mailer@example.com', 'mail_from_name' => 'WPS', 'enquiry_notify_email' => 'sales@example.com',
        ]];
        $this->put(route('admin.settings.update', 'mail'), $payload)->assertRedirect(route('admin.settings.edit', 'mail'));

        $stored = Setting::where('key', 'smtp_password')->value('value');
        $this->assertNotSame('app-secret-123', $stored);
        $this->assertSame('app-secret-123', Crypt::decryptString($stored));
        $this->get(route('admin.settings.edit', 'mail'))->assertDontSee('app-secret-123');

        // Blank password keeps the saved one
        $payload['settings']['smtp_password'] = '';
        $this->put(route('admin.settings.update', 'mail'), $payload);
        $this->assertSame('app-secret-123', Crypt::decryptString(Setting::where('key', 'smtp_password')->value('value')));

        Cache::forget(Setting::CACHE_KEY);
        MailSettings::apply();
        $this->assertSame('smtp', config('mail.default'));
        $this->assertSame('smtp.example.com', config('mail.mailers.smtp.host'));
        $this->assertSame(465, config('mail.mailers.smtp.port'));
        $this->assertSame('smtps', config('mail.mailers.smtp.scheme'));
        $this->assertSame('app-secret-123', config('mail.mailers.smtp.password'));
        $this->assertSame('mailer@example.com', config('mail.from.address'));

        // Invalid values are rejected
        $this->put(route('admin.settings.update', 'mail'), ['settings' => ['smtp_port' => 'abc', 'smtp_encryption' => 'weird']])
            ->assertSessionHasErrors(['settings.smtp_port', 'settings.smtp_encryption']);
    }

    public function test_enquiry_alert_email_is_sent_when_configured(): void
    {
        Mail::fake();
        Setting::where('key', 'smtp_host')->update(['value' => 'smtp.example.com']);
        Setting::where('key', 'enquiry_notify_email')->update(['value' => 'sales@example.com']);
        Cache::forget(Setting::CACHE_KEY);

        $this->post(route('contact.store'), [
            'name' => 'Ravi', 'mobile' => '9876543210', 'subject' => 'Liners', 'message' => 'Please send a quotation.',
        ])->assertSessionHas('success');

        Mail::assertSent(NewEnquiryMail::class, fn ($m) => $m->hasTo('sales@example.com'));
    }

    public function test_no_enquiry_alert_without_smtp(): void
    {
        Mail::fake();
        Setting::where('key', 'enquiry_notify_email')->update(['value' => 'sales@example.com']);
        Cache::forget(Setting::CACHE_KEY);

        $this->post(route('contact.store'), [
            'name' => 'Ravi', 'mobile' => '9876543210', 'subject' => 'Liners', 'message' => 'Please send a quotation.',
        ])->assertSessionHas('success');

        Mail::assertNothingSent();
        $this->assertDatabaseHas('enquiries', ['name' => 'Ravi']);
    }
}
