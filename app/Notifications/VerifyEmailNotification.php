<?php

namespace App\Notifications;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class VerifyEmailNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public readonly ?string $otpCode = null)
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
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(60),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );

        return (new MailMessage)
            ->subject('Confirm Your Email - Azza Respiratory Therapy')
            ->greeting('Hello ' . ($notifiable->name ?? 'User') . '!')
            ->line('Thank you for registering with Azza Respiratory Therapy.')
            ->line('Please click the button below to confirm your email address and activate your account.')
            ->action('Confirm Email', $verificationUrl)
            ->line('This confirmation link will expire in 60 minutes.')
            ->line('If you did not register for this account, please ignore this email.');
    }
}
