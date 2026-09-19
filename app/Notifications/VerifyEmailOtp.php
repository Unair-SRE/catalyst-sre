<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VerifyEmailOtp extends Notification
{
    use Queueable;

    public function __construct(public readonly string $code) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Verify your Catalyst email')
            ->greeting("Hello {$notifiable->name},")
            ->line('Use this six-digit code to verify your Catalyst account:')
            ->line($this->code)
            ->line('This code expires in five minutes and can only be used once.')
            ->line('If you did not create this account, you can ignore this email.');
    }
}
