<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Auth\Notifications\ResetPassword as Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Log;

class CustomResetPassword extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $resetUrl = url(config('app.url').route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        // Determine if this is an admin or organization
        $isAdmin = get_class($notifiable) === 'App\\Models\\Admin';
        
        Log::info('CustomResetPassword notification', [
            'user_type' => $isAdmin ? 'admin' : 'organization',
            'email' => $notifiable->getEmailForPasswordReset(),
            'reset_url' => $resetUrl
        ]);
        
        return (new MailMessage)
            ->subject('Reset Password Notification')
            ->line('You are receiving this email because we received a password reset request for your account.')
            ->action('Reset Password', $resetUrl)
            ->line('This password reset link will expire in ' . config('auth.passwords.'.($isAdmin ? 'admins' : 'organizations').'.expire') . ' minutes.')
            ->line('If you did not request a password reset, no further action is required.');
    }
}
