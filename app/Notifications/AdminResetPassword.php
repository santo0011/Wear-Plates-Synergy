<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

/** Password reset email for admin accounts, linking to the admin reset page. */
class AdminResetPassword extends ResetPassword
{
    public function toMail($notifiable): MailMessage
    {
        $url = route('admin.password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ]);
        $minutes = config('auth.passwords.admins.expire', 60);
        $site = setting('site_name', config('app.name'));

        return (new MailMessage)
            ->subject("Reset your admin password – {$site}")
            ->greeting("Hello {$notifiable->name},")
            ->line('We received a request to reset the password for your admin account.')
            ->action('Reset Password', $url)
            ->line("This link will expire in {$minutes} minutes.")
            ->line('If you did not request a password reset, you can ignore this email – your password will not change.')
            ->salutation("Regards,\n{$site}");
    }
}
