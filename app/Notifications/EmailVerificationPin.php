<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EmailVerificationPin extends Notification
{
    use Queueable;

    public function __construct(public readonly string $pin) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $digits = str_split($this->pin);

        return (new MailMessage)
            ->subject('Kode Verifikasi Email — EduLearn')
            ->view('emails.verify-pin', [
                'pin'      => $this->pin,
                'digits'   => $digits,
                'name'     => $notifiable->name,
                'expires'  => 10,
            ]);
    }
}
