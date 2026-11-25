<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VerifyEmailNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public readonly string $otpCode)
    {
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Email Verification OTP - Azza Respiratory Therapy')
            ->greeting('Hello ' . ($notifiable->name ?? 'User') . '!')
            ->line('Thank you for registering with Azza Respiratory Therapy.')
            ->line('Your 6-digit email verification OTP code is:')
            ->line('**' . $this->otpCode . '**')
            ->line('This code will expire in 15 minutes.')
            ->line('Please enter this OTP code in the application to verify your account and get started.')
            ->line('If you did not register for this account, please ignore this email.');
    }
}
