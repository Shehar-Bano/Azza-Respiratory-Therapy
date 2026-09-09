<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordOtpNotification extends Notification
{
    use Queueable;

    protected string $otpCode;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $otpCode)
    {
        $this->otpCode = $otpCode;
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
            ->subject('Password Reset OTP Code - Azza Respiratory Therapy')
            ->greeting('Hello ' . ($notifiable->name ?? 'User') . '!')
            ->line('We received a request to reset your password for your Azza Respiratory Therapy account.')
            ->line('Your 6-digit password reset verification code is:')
            ->line('**' . $this->otpCode . '**')
            ->line('This code will expire in 15 minutes.')
            ->line('If you did not request a password reset, please ignore this email or contact support if you have concerns.');
    }
}
